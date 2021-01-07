<?php declare(strict_types=1);

namespace Pollen\TinyMce;

use Exception;
use InvalidArgumentException;
use League\Route\Http\Exception\NotFoundException;
use RuntimeException;
use Pollen\TinyMce\Adapters\AdapterInterface;
use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\Plugins\FontawesomePlugin;
use Pollen\TinyMce\Plugins\GlyphsPlugin;
use Pollen\TinyMce\Plugins\TablePlugin;
use Pollen\TinyMce\Plugins\TemplatePlugin;
use Pollen\TinyMce\Plugins\VisualblocksPlugin;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Routing\Route;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Storage;

class TinyMce implements TinyMceContract
{
    use BootableTrait;
    use ContainerAwareTrait;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance de l'adapteur associé.
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    private $configBag;

    /**
     * Liste des plugins par défaut.
     * @var array
     */
    private $defaultPlugins = [
        'fontawesome'   => FontawesomePlugin::class,
        'glyphs'        => GlyphsPlugin::class,
        'table'         => TablePlugin::class,
        'template'      => TemplatePlugin::class,
        'visualblocks'  => VisualblocksPlugin::class
    ];

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Route de traitement des requêtes XHR.
     * @var Route|null
     */
    private $xhrRoute;

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
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): TinyMceContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): TinyMceContract
    {
        if (!$this->isBooted()) {
            events()->trigger('tiny-mce.booting', [$this]);

            $this->xhrRoute = Router::xhr(
                md5('tinyMce') . '/{plugin}/{controller}',
                [$this, 'xhrResponseDispatcher'],
                'GET'
            );

            foreach ($this->defaultPlugins as $alias => $pluginDefinition) {
                $this->registerPlugin($alias, $pluginDefinition);
            }

            $this->setBooted();

            events()->trigger('tiny-mce.booted', [$this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if (!isset($this->configBag) || is_null($this->configBag)) {
            $this->configBag = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->configBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->configBag->set($key);
        } else {
            return $this->configBag;
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchToolbarButtons(string $buttonsDefinition): TinyMceContract
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
    public function getAdapter(): ?AdapterInterface
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
        } elseif (isset($params['driver'])) {
            $this->registerPlugin($alias, $params['driver']);
            unset($params['driver']);
        }
        $plugin = $this->getPluginFromDefinition($alias);

        if (!$plugin instanceof PluginDriverInterface) {
            return null;
        }
        return $this->plugins[$alias] = $plugin->setAlias($alias)->setParams($params);
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
        } elseif (is_string($def) && $this->containerHas($def)) {
            if ($this->containerHas($def)) {
                return $this->containerGet($def);
            }
        } elseif(is_string($def) && class_exists($def)) {
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
    public function getXhrRouteUrl(string $plugin, ?string $controller = null, array $params = []): string
    {
        $controller = $controller ?? 'xhrResponse';

        return $this->xhrRoute->getUrl(array_merge($params, compact('plugin', 'controller')));
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
    public function loadPlugins(): TinyMceContract
    {
        foreach ($this->config('plugins', []) as $alias => $params) {
            if (is_numeric($alias)) {
                $alias  = (string)$params;
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
    public function registerDefaultPlugin(string $alias, $pluginDefinition): TinyMceContract
    {
        $this->defaultPlugins[$alias] = $pluginDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerPlugin(string $alias, $pluginDefinition): TinyMceContract
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
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources');
        }
        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(AdapterInterface $adapter): TinyMceContract
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMceInit(array $mceInit): TinyMceContract
    {
        $this->mceInit = array_merge($this->mceInit, $mceInit);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): TinyMceContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseDispatcher(string $pluginAlias, string $controller, ...$args): array
    {
        try {
            $plugin = $this->getPlugin($pluginAlias);
        } catch(Exception $e) {
            throw new NotFoundException(
                sprintf('TinyMce Plugin [%s] return exception : %s', $pluginAlias, $e->getMessage())
            );
        }
        try {
            return $plugin->{$controller}(...$args);
        } catch(Exception $e) {
            throw new NotFoundException(
                sprintf('TinyMce Plugin [%s] Controller [%s] call return exception', $controller, $pluginAlias)
            );
        }
    }
}
