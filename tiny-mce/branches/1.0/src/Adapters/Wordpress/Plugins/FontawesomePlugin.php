<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters\Wordpress\Plugins;

use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\Adapters\Wordpress\GlyphsPluginDriver;
use tiFy\Support\Proxy\Asset;

class FontawesomePlugin extends GlyphsPluginDriver
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
                    "let fontAwesomeChars=" . wp_json_encode($this->parseGlyphs()) .
                    ",tinymceFontAwesomel10n={'title':'{$this->params('title')}'};",
                    true
                );
                if (isset($this->glyphs[$this->params('button')])) {
                    Asset::setInlineCss(
                        "i.mce-i-fontawesome:before{content:'{$this->glyphs[$this->params('button')]}';}" .
                        "i.mce-i-fontawesome:before,.mce-grid a.fontawesome{" .
                        "font-family:'{$this->params('font-family')}'!important;}"
                    );
                }
            }
        );

        add_action('wp_head', function () {
            asset()->setInlineCss(".fontawesome{font-family:'{$this->params('font-family')}';}");
        });
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
                 * @var bool $admin_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères dans l'interface d'administration (bouton).
                 */
                'admin_enqueue_scripts'  => true,
                /**
                 * @var string $button Nom du glyph utilisé pour illustré le bouton de l'éditeur TinyMCE.
                 */
                'button'      => 'flag',
                /**
                 * @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
                 */
                'cols'        => 32,
                /**
                 * @var bool $editor_enqueue_font Activation de la mise en file automatique des styles du plugin.
                 */
                'editor_enqueue_font' => true,
                /**
                 * @var string $font-family Nom d'appel de la Famille de la police de caractères.
                 */
                'font-family' => 'fontAwesome',
                /**
                 * @var string $path Chemin absolu ou relatif vers la feuille de style CSS.
                 */
                'glyphs'     => '/vendor/fortawesome/font-awesome/css/font-awesome.css',
                /**
                 * @var string $hookname Nom d'accroche pour la mise en file de la police de caractères.
                 */
                'hookname'    => 'font-awesome',
                /**
                 * @var string $prefix Préfixe des classes de la police de caractères.
                 */
                'prefix'      => 'fa-',
                /**
                 * @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
                 */
                'title'       => __('Police de caractères fontAwesome', 'tify'),
            ]
        );
    }
}
