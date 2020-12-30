<?php declare(strict_types=1);

namespace Pollen\TinyMce;

use RuntimeException;
use Pollen\TinyMce\Adapters\AdapterInterface;
use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\Plugins\PluginInterface;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\ParamsBag;
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
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Liste des attributs de configuration complémentaires.
     * @var array
     */
    protected $additionnalConfig = [];

    /**
     * Liste des plugins externes déclarés.
     * @var PluginInterface[]|array
     */
    protected $plugins = [];

    /**
     * Liste des boutons de plugin externes configuré dans la toolbar.
     * @var string[]
     */
    protected $toolbarButtons = [];

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
    public function getAdapter(): ?AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getPluginAssetsUrl(string $name): string
    {
        $cinfo = class_info($this);

        return (is_dir($cinfo->getDirname() . "/Resources/assets/plugins/{$name}"))
            ? $cinfo->getUrl() . "/Resources/assets/plugins/{$name}"
            : '';
    }

    /**
     * @inheritDoc
     */
    public function getPluginUrl(string $name): string
    {
        $cinfo = class_info($this);

        return (file_exists($cinfo->getDirname() . "/Resources/assets/plugins/{$name}/plugin.js"))
            ? $cinfo->getUrl() . "/Resources/assets/plugins/{$name}/plugin.js"
            : '';
    }

    /**
     * @inheritDoc
     */
    public function fetchPluginsButtons($buttons = ''): void
    {
        $exists = preg_split('#\||\s#', $buttons, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($exists as $name) {
            if (isset($this->externalPlugins[$name])) {
                $this->toolbarButtons[] = $name;
            }
        }
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
    public function setAdditionnalConfig(array $config): TinyMceContract
    {
        $this->additionnalConfig = array_merge($this->additionnalConfig, $config);

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
    public function setPlugin(PluginInterface $plugin): TinyMceContract
    {
        $this->plugins[$plugin->getName()] = $plugin;

        return $this;
    }
}
