<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters\Wordpress\Plugins;

use Pollen\TinyMce\Adapters\Wordpress\GlyphsPluginDriver;
use Pollen\TinyMce\Contracts\TinyMceContract;
use tiFy\Support\Proxy\Asset;

class DashiconsPlugin extends GlyphsPluginDriver
{
    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
        parent::__construct($tinyMceManager);

        add_action(
            'admin_enqueue_scripts',
            function () {
                Asset::setInlineJs(
                    "let dashiconsChars=" . json_encode($this->parseGlyphs()) .
                    ",tinymceDashiconsl10n={'title':'{$this->params('title')}'};",
                    true
                );
                if (isset($this->glyphs[$this->params('button')])) {
                    Asset::setInlineCss(
                        "i.mce-i-dashicons:before{" .
                        "content:'" . ($this->glyphs[$this->params('button')]
                            ? $this->glyphs[$this->params('button')] : '') . "';}" .
                        "i.mce-i-dashicons:before,.mce-grid a.dashicons{" .
                        "font-family:'{$this->params('font-family')}'!important;}"
                    );
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
                 * @var string $button Nom du glyph utilisé pour illustré le bouton de l'éditeur TinyMCE.
                 */
                'button'      => 'wordpress-alt',
                /**
                 * @var string $font-family Nom d'appel de la Famille de la police de caractères.
                 */
                'font-family' => 'dashicons',
                /**
                 * @var string $glyphes Chemin absolu ou relatif vers la feuille de style CSS.
                 */
                'glyphs'        => '/' . WPINC . '/css/dashicons.css',
                /**
                 * @var string $hookname Nom d'accroche pour la mise en file de la police de caractères.
                 */
                'hookname'    => 'dashicons',
                /**
                 * @var string $prefix Préfixe des classes de la police de caractères.
                 */
                'prefix'      => 'dashicons-',
                /**
                 * @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
                 */
                'title'                  => __('Police de caractères Wordpress', 'tify'),
            ]
        );
    }
}
