<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

interface GlyphsPluginDriverInterface extends PluginDriverInterface
{
    /**
     * Récupération de la liste des glyphs depuis la feuille de style CSS.
     *
     * @return static
     */
    public function fetchGlyphs(): GlyphsPluginDriverInterface;

    /**
     * Récupération du chemin relatif vers la feuille de style CSS.
     *
     * @return string|null
     */
    public function getGlyphsRelPath(): ?string;

    /**
     * Traitement des glyphs récupérés depuis la feuille de style CSS.
     *
     * @return array
     */
    public function parseGlyphs(): array;
}