<?php declare(strict_types=1);

namespace Pollen\TinyMce;

use tiFy\Container\ServiceProvider;
use Pollen\TinyMce\Adapters\WordpressAdapter;
use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\Plugins\FontawesomePlugin;
use Pollen\TinyMce\Plugins\GlyphsPlugin;
use Pollen\TinyMce\Plugins\TablePlugin;
use Pollen\TinyMce\Plugins\TemplatePlugin;
use Pollen\TinyMce\Plugins\VisualblocksPlugin;

class TinyMceServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        TinyMceContract::class,
        FontawesomePlugin::class,
        GlyphsPlugin::class,
        TablePlugin::class,
        TemplatePlugin::class,
        VisualblocksPlugin::class,
        WordpressAdapter::class
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        events()->listen(
            'wp.booted',
            function () {
                /** @var TinyMceContract $tinyMce */
                $tinyMce = $this->getContainer()->get(TinyMceContract::class);
                $tinyMce->setAdapter($this->getContainer()->get(WordpressAdapter::class))->boot();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            TinyMceContract::class,
            function () {
                return new TinyMce(config('tiny-mce', []), $this->getContainer());
        });
        $this->registerAdapters();
        $this->registerPlugins();
    }

    /**
     * Déclaration des adapteurs.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(
            WordpressAdapter::class,
            function () {
                return new WordpressAdapter($this->getContainer()->get(TinyMceContract::class));
            }
        );
    }

    /**
     * Déclaration des controleurs de plugins.
     *
     * @return void
     */
    public function registerPlugins(): void
    {
        $this->getContainer()->add(FontawesomePlugin::class, function () {
            return new FontawesomePlugin($this->getContainer()->get(TinyMceContract::class));
        });
        $this->getContainer()->add(GlyphsPlugin::class, function () {
            return new GlyphsPlugin($this->getContainer()->get(TinyMceContract::class));
        });
        $this->getContainer()->add(TablePlugin::class, function () {
            return new TablePlugin($this->getContainer()->get(TinyMceContract::class));
        });
        $this->getContainer()->add(TemplatePlugin::class, function () {
            return new TemplatePlugin($this->getContainer()->get(TinyMceContract::class));
        });
        $this->getContainer()->add(VisualblocksPlugin::class, function () {
            return new VisualblocksPlugin($this->getContainer()->get(TinyMceContract::class));
        });
    }
}