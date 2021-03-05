<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters\Wordpress;

use Pollen\TinyMce\GlyphsPluginDriver as BaseGlyphsPluginDriver;
use Pollen\TinyMce\TinyMceInterface;

abstract class GlyphsPluginDriver extends BaseGlyphsPluginDriver
{
    /**
     * @param TinyMceInterface $tinyMce
     */
    public function __construct(TinyMceInterface $tinyMce)
    {
        parent::__construct($tinyMce);

        add_action(
            'init',
            function () {
                $this->fetchGlyphs();

                if ($path = $this->getGlyphsRelPath()) {
                    wp_register_style(
                        $this->getHookname(),
                        Url::root($path)->render()
                    );
                }
            }
        );

        add_action(
            'admin_enqueue_scripts',
            function () {
                if ($this->params('editor_enqueue_font')) {
                    wp_enqueue_style($this->getHookname());
                }
            }
        );

        add_filter(
            'mce_css',
            function (string $mce_css) {
                if ($this->params('editor_enqueue_font') && ($path = $this->getGlyphsRelPath())) {
                    $mce_css .= ', ' . Url::root($path)->render();
                }
                return $mce_css;
            }
        );

        add_action(
            'wp_enqueue_scripts',
            function () {
                if ($this->params('theme_enqueue_font')) {
                    wp_enqueue_style($this->getHookname());
                }
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var string|null $hookname Nom d'accroche pour la mise en file de la police de caractères.
                 */
                'hookname'            => null,
                /**
                 * @var bool $editor_enqueue_font Activation de la mise en file automatique de la font dans l'éditeur.
                 */
                'editor_enqueue_font' => false,
                /**
                 * @var bool $theme_enqueue_font Activation de la mise en file automatique de la font dans le theme.
                 */
                'theme_enqueue_font'  => false,
            ]
        );
    }

    /**
     * Récupération du nom de qualification pour la mise en file de la police de caractères.
     *
     * @return string
     */
    public function getHookname(): string
    {
        return $this->params('hookname') ?: md5($this->getAlias());
    }
}