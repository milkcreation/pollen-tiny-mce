<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

use Illuminate\Support\Collection;

abstract class AbstractGlyphsPlugin extends AbstractPlugin
{
    /**
     * Liste des glyphs contenu dans la feuille de style de la police glyphs.
     * @var array
     */
    protected $glyphs = [];

    /**
     * @inheritDoc
     */
    public function defaultParams() : array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string $path Chemin vers le fichier CSS de la police de caractère. Doit être non minifiée.
             */
            'path'                   => '/' . WPINC . '/css/dashicons.css',
            /**
             * @var string $button Nom du glyph utilisé pour illustré le bouton de l'éditeur TinyMCE.
             */
            'button'                 => 'wordpress-alt',
            /**
             * @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
             */
            'cols'                   => 24,
            /**
             *  @var string $font-family Nom d'appel de la Famille de la police de caractères.
             */
            'font-family'            => 'dashicons',
            /**
             * @var string $prefix Préfixe des classes de la police de caractères.
             */
            'prefix'                 => 'dashicons-',
            /**
             * @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
             */
            'title'                  => __('Police de caractères', 'tify'),
            /**
             * @var string $version Numéro de version utilisé lors de la mise en file de la feuille de style de la police de caractères. La mise en file auto doit être activée.
             */
            'version'                => current_time('timestamp'),
        ]);
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @param string $mce_css Liste des url vers les feuilles de styles associées à tinyMCE.
     *
     * @return string
     */
    public function mceCss($mce_css)
    {
        if ($this->params('editor_enqueue_scripts')) {
            $mce_css .= ', ' . url()->root($this->get('path'));
        }
        return $mce_css . ', ' . $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/editor.css';
    }

    /**
     * Traitement de récupération des glyphs depuis le fichier CSS.
     *
     * @return array
     */
    public function parseGlyphs()
    {
        $items = array_map(function ($value) {
            return preg_replace('#' . preg_quote('\\') . '#', '&#x', $value);
        }, $this->glyphs);

        return (new Collection($items))->chunk($this->params('cols'))->toArray();
    }
}