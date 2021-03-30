<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Exception;
use InvalidArgumentException;
use League\Route\Http\Exception\NotFoundException;
use Pollen\Routing\RouteInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Filesystem;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\EventProxy;
use Pollen\Support\Proxy\RouterProxy;
use RuntimeException;
use Pollen\TinyMce\Plugins\FontawesomePlugin;
use Pollen\TinyMce\Plugins\GlyphsPlugin;
use Pollen\TinyMce\Plugins\TablePlugin;
use Pollen\TinyMce\Plugins\TemplatePlugin;
use Pollen\TinyMce\Plugins\VisualblocksPlugin;
use Psr\Container\ContainerInterface as Container;


class TinyMce implements TinyMceInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;
    use EventProxy;
    use RouterProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance de l'adapteur associé.
     * @var TinyMceAdapterInterface
     */
    private $adapter;

    /**
     * Liste des plugins par défaut.
     * @var array
     */
    private $defaultPlugins = [
        'fontawesome'  => FontawesomePlugin::class,
        'glyphs'       => GlyphsPlugin::class,
        'table'        => TablePlugin::class,
        'template'     => TemplatePlugin::class,
        'visualblocks' => VisualblocksPlugin::class,
    ];

    /**
     * Chemin vers le répertoire des ressources.
     * @var string|null
     */
    protected $resourcesBaseDir;

    /**
     * Route de traitement des requêtes XHR.
     * @var RouteInterface|null
     */
    protected $xhrRoute;

    /**
     * Liste des boutons de plugin déclarés.
     * @var string[]
     */
    protected $buttons = [];

    /**
     * Paramètres de configuration généraux de tinyMce.
     * @var array
     */
    protected $mceInit = [];

    /**
     * Liste des plugins externes déclarés.
     * @var PluginDriverInterface[]|array
     */
    protected $plugins = [];

    /**
     * Liste des plugins externes déclarés.
     * @var PluginDriverInterface[]|string[]|array
     */
    protected $pluginDefinitions = [];

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): TinyMceInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): TinyMceInterface
    {
        if (!$this->isBooted()) {
            $this->event()->trigger('tiny-mce.booting', [$this]);

            if ($router = $this->router()) {
                $this->xhrRoute = $router->xhr(
                    '/api/' . md5('tinyMce') . '/{plugin}/{controller}',
                    [$this, 'xhrResponseDispatcher']
                );
            }

            foreach ($this->defaultPlugins as $alias => $pluginDefinition) {
                $this->registerPlugin($alias, $pluginDefinition);
            }

            $this->setBooted();

            $this->event()->trigger('tiny-mce.booted', [$this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchToolbarButtons(string $buttonsDefinition): TinyMceInterface
    {
        $exists = preg_split('#\||\s#', $buttonsDefinition, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($exists as $alias) {
            if ($this->hasPlugin($alias)) {
                $this->buttons[] = $alias;
            }
        }
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getAdapter(): ?TinyMceAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getMceInit(): array
    {
        return $this->mceInit;
    }

    /**
     * @inheritDoc
     */
    public function getPlugin(string $alias, array $params = []): ?PluginDriverInterface
    {
        if ($this->hasPlugin($alias)) {
            return $this->plugins[$alias];
        }

        if (isset($params['driver'])) {
            $this->registerPlugin($alias, $params['driver']);
            unset($params['driver']);
        }
        $plugin = $this->getPluginFromDefinition($alias);

        if (!$plugin instanceof PluginDriverInterface) {
            return null;
        }

        $this->plugins[$alias] = $plugin->setAlias($alias);
        $this->plugins[$alias]->setParams($params);

        return $this->plugins[$alias];
    }

    /**
     * Récupération d'une instance de pilote depuis une définition.
     *
     * @param string $alias
     *
     * @return PluginDriverInterface|null
     */
    protected function getPluginFromDefinition(string $alias): ?PluginDriverInterface
    {
        if (!$def = $this->pluginDefinitions[$alias] ?? null) {
            throw new InvalidArgumentException(sprintf('Plugin with alias [%s] unavailable', $alias));
        }

        if ($def instanceof PluginDriverInterface) {
            return clone $def;
        }

        if (is_string($def) && $this->containerHas($def)) {
            return $this->containerGet($def);
        }

        if (is_string($def) && class_exists($def)) {
            return new $def($this);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @inheritDoc
     */
    public function getXhrRouteUrl(string $plugin, ?string $controller = null, array $params = []): ?string
    {
        if ($this->xhrRoute instanceof RouteInterface && ($router = $this->router())) {
            $controller = $controller ?? 'xhrResponse';

            return $router->getRouteUrl($this->xhrRoute, array_merge($params, compact('plugin', 'controller')));
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasButton(string $button): bool
    {
        return isset($this->buttons[$button]);
    }

    /**
     * @inheritDoc
     */
    public function hasPlugin(string $alias): bool
    {
        return isset($this->plugins[$alias]);
    }

    /**
     * @inheritDoc
     */
    public function loadPlugins(): TinyMceInterface
    {
        foreach ($this->config('plugins', []) as $alias => $params) {
            if (is_numeric($alias)) {
                $alias = (string)$params;
                $params = [];
            }

            if ($plugin = $this->getPlugin($alias, $params)) {
                $plugin->boot();
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerDefaultPlugin(string $alias, $pluginDefinition): TinyMceInterface
    {
        $this->defaultPlugins[$alias] = $pluginDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerPlugin(string $alias, $pluginDefinition): TinyMceInterface
    {
        if (isset($this->pluginDefinitions[$alias])) {
            throw new RuntimeException(sprintf('Another Plugin with alias [%s] already registered', $alias));
        }

        $this->pluginDefinitions[$alias] = $pluginDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null): string
    {
        if ($this->resourcesBaseDir === null) {
            $this->resourcesBaseDir = Filesystem::normalizePath(
                realpath(
                    dirname(__DIR__) . '/resources/'
                )
            );

            if (!file_exists($this->resourcesBaseDir)) {
                throw new RuntimeException('Field ressources directory unreachable');
            }
        }

        return is_null($path) ? $this->resourcesBaseDir : $this->resourcesBaseDir . Filesystem::normalizePath($path);
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(TinyMceAdapterInterface $adapter): TinyMceInterface
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMceInit(array $mceInit): TinyMceInterface
    {
        $this->mceInit = array_merge($this->mceInit, $mceInit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setResourcesBaseDir(string $resourceBaseDir): TinyMceInterface
    {
        $this->resourcesBaseDir = Filesystem::normalizePath($resourceBaseDir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseDispatcher(string $pluginAlias, string $controller, ...$args): array
    {
        try {
            $plugin = $this->getPlugin($pluginAlias);
        } catch (Exception $e) {
            throw new NotFoundException(
                sprintf('TinyMce Plugin [%s] return exception : %s', $pluginAlias, $e->getMessage())
            );
        }
        if ($plugin !== null) {
            try {
                return $plugin->{$controller}(...$args);
            } catch (Exception $e) {
                throw new NotFoundException(
                    sprintf('TinyMce Plugin [%s] Controller [%s] call return exception', $controller, $pluginAlias)
                );
            }
        }

        throw new NotFoundException(
            sprintf('TinyMce Plugin [%s] unreachable', $pluginAlias)
        );
    }
}
