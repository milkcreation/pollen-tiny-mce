<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters\Wordpress\Plugins;

use Illuminate\Support\Collection;
use Pollen\TinyMce\Contracts\TinyMceContract;

abstract class AbstractGlyphsPlugin extends AbstractPlugin
{
    /**
     * Liste des glyphs contenu dans la feuille de style de la police glyphs.
     * @var array
     */
    protected $glyphs = [];

    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
        parent::__construct($tinyMceManager);

        add_action('admin_init', function () {
            if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
                add_filter('mce_css', [$this, 'mce_css']);
            }
        });

        add_action('admin_enqueue_scripts', function () {
            if ($this->params('admin_enqueue_scripts')) {
                wp_enqueue_style($this->params('hookname'));
            }

            wp_enqueue_style('tiFyTinyMceExternalPlugins' . class_info($this)->getShortName());

            asset()->setInlineJs(
                "let dashiconsChars=" . json_encode($this->parseGlyphs()) .
                ",tinymceDashiconsl10n={'title':'{$this->params('title')}'};",
                true
            );

            asset()->setInlineCss(
                "i.mce-i-dashicons:before{" .
                "content:'" . ($this->glyphs[$this->params('button')] ? $this->glyphs[$this->params('button')] : '') . "';}" .
                "i.mce-i-dashicons:before,.mce-grid a.dashicons{font-family:'{$this->params('font-family')}'!important;}"
            );
        });

        add_action('init', function () {
            wp_register_style(
                $this->params('hookname'),
                url()->root($this->params('path')),
                $this->params('dependencies'),
                $this->params('version')
            );
            wp_register_style(
                'tiFyTinyMceExternalPlugins' . class_info($this)->getShortName(),
                $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/plugin.css',
                [],
                $this->params('version')
            );

            $path = (preg_match('/^'. preg_quote(ABSPATH, DIRECTORY_SEPARATOR) . '/', $this->params('path'), $match))
                ?  $this->params('path') : ABSPATH. $this->params('path');

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
        });

        add_action('wp_enqueue_scripts', function () {
            if ($this->params('wp_enqueue_scripts') && $this->isEnabled()) {
                wp_enqueue_style($this->params('hookname'));
            }
            asset()->setInlineCss(".{$this->name}{font-family:'{$this->params('font-family')}';}");
        });
    }

    /**
     * @inheritDoc
     */
    public function defaultParams() : array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string $hookname Nom d'accroche pour la mise en file de la police de caractères.
             */
            'hookname'               => 'dashicons',
            /**
             * @var bool $admin_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères dans l'interface d'administration (bouton).
             */
            'admin_enqueue_scripts'  => false,
            /**
             * @var bool $editor_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères dans l'éditeur.
             */
            'editor_enqueue_scripts' => true,
            /**
             * @var bool $wp_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères.
             */
            'wp_enqueue_scripts'     => false
        ]);
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @param string $mce_css Liste des url vers les feuilles de styles associées à tinyMCE.
     *
     * @return string
     */
    public function mceCss(string $mce_css)
    {
        if ($this->params('editor_enqueue_scripts')) {
            $mce_css .= ', ' . url()->root($this->params('path'));
        }
        return $mce_css . ', ' . $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/editor.css';
    }

    /**
     * Traitement de récupération des glyphs depuis le fichier CSS.
     *
     * @return array
     */
    public function parseGlyphs(): array
    {
        $items = array_map(function ($value) {
            return preg_replace('#' . preg_quote('\\') . '#', '&#x', $value);
        }, $this->glyphs);

        return (new Collection($items))->chunk($this->params('cols'))->toArray();
    }
}