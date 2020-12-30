<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;


class JumplinePlugin extends AbstractPlugin
{
    /**
     * @inheritDoc
     */
    public function boot()
    {
        add_action('admin_init', [$this, 'admin_init']);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
    }

    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'wp_enqueue_scripts' => false
        ];
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
            add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
            add_filter('mce_css', [$this, 'mce_css']);
        }
    }

    /**
     * Mise en file de scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        asset()->setInlineJs("let tiFyTinyMCEJumpLinel10n={'title':'" . __('Saut de ligne', 'tify') . "'};", true);

        asset()->setInlineCss("i.mce-i-jumpline::before{content:'\\f474';font-family:'dashicons';}");
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @param string $mce_css Liste des url vers les feuilles de styles associées à tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        return $mce_css . ', ' . $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/editor.css';
    }

    /**
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        if ($this->get('wp_enqueue_scripts') && $this->isActive()) {
            wp_enqueue_style(
                'TinyMceExternalPluginsJumpLine',
                $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/styles.css',
                [],
                160625
            );
        }
    }
}
