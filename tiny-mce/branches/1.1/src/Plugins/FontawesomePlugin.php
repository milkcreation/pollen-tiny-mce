<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

use Pollen\TinyMce\GlyphsPluginDriver;

class FontawesomePlugin extends GlyphsPluginDriver
{
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
                'button'      => 'flag',
                /**
                 * @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
                 */
                'cols'        => 32,
                /**
                 * @var string $font-family Nom d'appel de la Famille de la police de caractères.
                 */
                'font-family' => 'fontAwesome',
                /**
                 * @var string $path Chemin absolu ou relatif vers la feuille de style CSS.
                 */
                'glyphs'     => '/vendor/fortawesome/font-awesome/css/font-awesome.css',
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
