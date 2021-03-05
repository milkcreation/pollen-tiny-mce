<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Illuminate\Support\Collection;

abstract class GlyphsPluginDriver extends PluginDriver implements GlyphsPluginDriverInterface
{
    /**
     * Liste des glyphs déclarés dans la feuille de styles CSS.
     * @var array
     */
    protected $glyphs = [];

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
                 */
                'cols'    => 24,
                /**
                 * @var string|null $path Chemin vers le fichier CSS de la police de caractère. Doit être non minifiée.
                 */
                'glyphs'  => null,
                /**
                 * @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
                 */
                'title'   => __('Police de caractères', 'tify'),
                /**
                 * @var string $version Numéro de version utilisé lors de la mise en file de la feuille de style de la police de caractères. La mise en file auto doit être activée.
                 */
                'version' => current_time('timestamp'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getGlyphsRelPath(): ?string
    {
        $glyphs = $this->params('glyphs');

        if (is_string($glyphs) && preg_match(
                '/^' . preg_quote(ROOT_PATH, DIRECTORY_SEPARATOR) . '/',
                $glyphs,
                $match
            )) {
            return $match[1];
        } elseif (file_exists(ROOT_PATH . $glyphs)) {
            return $glyphs;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchGlyphs(): GlyphsPluginDriverInterface
    {
        if (!$path = $this->getGlyphsRelPath()) {
            return $this;
        }
        $path = ROOT_PATH . $path;

        if (!file_exists($path)) {
            return $this;
        }

        $contents = file_get_contents($path);

        preg_match_all(
            "#." . $this->params('prefix') . "(.*):before\s*\{\s*content\:\s*\"(.*)\";\s*\}\s*#",
            $contents,
            $matches
        );

        if (isset($matches[1])) {
            foreach ($matches[1] as $i => $class) {
                if (isset($matches[2][$i])) {
                    $this->glyphs[$class] = $matches[2][$i];
                }
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseGlyphs(): array
    {
        $items = array_map(
            function ($value) {
                return preg_replace('#' . preg_quote('\\') . '#', '&#x', $value);
            },
            $this->glyphs
        );

        return (new Collection($items))->chunk($this->params('cols'))->toArray();
    }
}