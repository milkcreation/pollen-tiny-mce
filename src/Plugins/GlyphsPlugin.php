<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

use Pollen\TinyMce\GlyphsPluginDriver;

/**
 * @todo
 */
class GlyphsPlugin extends GlyphsPluginDriver
{
    /**
     * Mise en file de scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        if ($this->get('admin_enqueue_scripts')) {
            wp_enqueue_style($this->get('hookname'));
        }

        wp_enqueue_style('tiFyTinyMceExternalPlugins' . class_info($this)->getShortName());

        asset()->setInlineJs(
            "let glyphs=" . wp_json_encode($this->parseGlyphs()) . "," .
            "tinymceOwnglyphsl10n={'title':'{$this->get('title')}'};",
            true
        );

        asset()->setInlineCss(
            "i.mce-i-ownglyphs::before{content:'{$this->glyphs[$this->get('button')]}';}" .
            "i.mce-i-ownglyphs::before,.mce-grid a.ownglyphs{font-family:'{$this->get('font-family')}'!important;}"
        );
    }

    /**
     * Action Ajax.
     *
     * @return string
     */
    public function wp_ajax()
    {
        header("Content-type: text/css");
        echo '.ownglyphs{font-family:' . $this->get('font-family') . ';}';
        exit;
    }
}
