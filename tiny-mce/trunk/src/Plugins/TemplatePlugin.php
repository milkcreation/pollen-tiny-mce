<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

class TemplatePlugin extends AbstractPlugin
{
    /**
     * @inheritDoc
     */
    public function boot()
    {
        add_filter('mce_css', [$this, 'mce_css']);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
        add_action('wp_ajax_tinymce_plugin_template', [$this, 'wp_ajax']);
    }

    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'wp_enqueue_scripts' => false,
            'mce_init'           => [
                'templates' => add_query_arg([
                    'action' => 'tinymce_plugin_template',
                    'nonce'  => wp_create_nonce('TinyMcePluginTemplate')
                ], admin_url('admin-ajax.php'))
            ]
        ];
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
     * Action ajax.
     *
     * @return string
     */
    public function wp_ajax()
    {
        if ( ! wp_verify_nonce($_GET['nonce'], 'TinyMcePluginTemplate')) {
            return;
        }

        nocache_headers();

        header('Content-Type: application/x-javascript; charset=UTF-8');

        $url = class_info($this)->getUrl();

        echo json_encode([
            [
                "title"       => "2 Colonnes : 1/4, 3/4",
                "description" => "1 colonne d'1/4 et l'autre de 3/4",
                "url"         => $url . "/templates/2cols_0.25-0.75.htm"
            ],
            [
                "title"       => "2 Colonnes : 1/3, 2/3",
                "description" => "1 colonne d'1/3 et l'autre de 2/3",
                "url"         => $url . "/templates/2cols_0.33-0.66.htm"
            ],
            [
                "title"       => "2 Colonnes : 1/2, 1/2",
                "description" => "1 colonnes d'1/2 et l'autre d'1/2",
                "url"         => $url . "/templates/2cols_0.5-0.5.htm"
            ],
            [
                "title"       => "2 Colonnes : 2/3, 1/3",
                "description" => "1 colonne de 2/3 et l'autre d'1/3",
                "url"         => $url . "/templates/2cols_0.66-0.33.htm"
            ],
            [
                "title"       => "2 Colonnes : 3/4, 1/4",
                "description" => "1 colonne de 3/4 et l'autre d'1/4",
                "url"         => $url . "/templates/2cols_0.75-0.25.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/4, 1/4, 1/2",
                "description" => "1 colonne d'1/4, une d'1/4 et une d'1/2",
                "url"         => $url . "/templates/3cols_0.25-0.25-0.5.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/4, 1/2, 1/4",
                "description" => "1 colonne d'1/4, une d'1/2 et une d'1/4",
                "url"         => $url . "/templates/3cols_0.25-0.5-0.25.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/3, 1/3, 1/3",
                "description" => "1 colonne d'1/3, une d'1/3 et une d'1/3",
                "url"         => $url . "/templates/3cols_0.33-0.33-0.33.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/2, 1/4, 1/4",
                "description" => "1 colonne d'1/2, une d'1/4 et une d'1/4",
                "url"         => $url . "/templates/3cols_0.5-0.25-0.25.htm"
            ],
            [
                "title"       => "4 Colonnes : 1/4, 1/4, 1/4, 1/4",
                "description" => "1 colonnes d'1/4, une d'1/4, une d'1/4 et une d'1/4",
                "url"         => $url . "/templates/4cols_0.25-0.25-0.25-0.25.htm"
            ]
        ]);
        exit;
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
                'TinyMceExternalPluginsTemplate',
                $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/styles.css',
                [],
                150317
            );
        }
    }
}
