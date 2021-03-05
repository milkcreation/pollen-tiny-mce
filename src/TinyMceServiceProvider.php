<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Pollen\Container\BaseServiceProvider;
use Pollen\TinyMce\Adapters\WordpressAdapter;
use Pollen\TinyMce\Plugins\FontawesomePlugin;
use Pollen\TinyMce\Plugins\GlyphsPlugin;
use Pollen\TinyMce\Plugins\TablePlugin;
use Pollen\TinyMce\Plugins\TemplatePlugin;
use Pollen\TinyMce\Plugins\VisualblocksPlugin;

class TinyMceServiceProvider extends BaseServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        TinyMceInterface::class,
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
    public function register(): void
    {
        $this->getContainer()->share(
            TinyMceInterface::class,
            function () {
                return new TinyMce([], $this->getContainer());
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
                return new WordpressAdapter($this->getContainer()->get(TinyMceInterface::class));
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
            return new FontawesomePlugin($this->getContainer()->get(TinyMceInterface::class));
        });
        $this->getContainer()->add(GlyphsPlugin::class, function () {
            return new GlyphsPlugin($this->getContainer()->get(TinyMceInterface::class));
        });
        $this->getContainer()->add(TablePlugin::class, function () {
            return new TablePlugin($this->getContainer()->get(TinyMceInterface::class));
        });
        $this->getContainer()->add(TemplatePlugin::class, function () {
            return new TemplatePlugin($this->getContainer()->get(TinyMceInterface::class));
        });
        $this->getContainer()->add(VisualblocksPlugin::class, function () {
            return new VisualblocksPlugin($this->getContainer()->get(TinyMceInterface::class));
        });
    }
}