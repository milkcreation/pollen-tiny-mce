<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters;

use Pollen\TinyMce\Adapters\Wordpress\Plugins\DashiconsPlugin;
use Pollen\TinyMce\Adapters\Wordpress\Plugins\FontawesomePlugin;
use Pollen\TinyMce\Adapters\Wordpress\Plugins\JumplinePlugin;
use Pollen\TinyMce\PluginDriverInterface;
use Pollen\TinyMce\TinyMceInterface;
use Pollen\TinyMce\AbstractTinyMceAdapter;
use WP_User;

class WordpressAdapter extends AbstractTinyMceAdapter
{
    /**
     * @param TinyMceInterface $tinyMce
     */
    public function __construct(TinyMceInterface $tinyMce)
    {
        parent::__construct($tinyMce);

        $this->tinyMce()
            ->registerDefaultPlugin('dashicons', DashiconsPlugin::class)
            ->registerDefaultPlugin('fontawesome', FontawesomePlugin::class)
            ->registerDefaultPlugin('jumpline', JumplinePlugin::class);

        $this->tinyMce()->event()->on(
            'tiny-mce.booting',
            function () {
                if ($container = $this->tinyMce()->getContainer()) {
                    $container->add(
                        DashiconsPlugin::class,
                        function () {
                            return new DashiconsPlugin($this->tinyMce());
                        }
                    );
                    $container->add(
                        FontawesomePlugin::class,
                        function () {
                            return new FontawesomePlugin($this->tinyMce());
                        }
                    );
                    $container->add(
                        JumplinePlugin::class,
                        function () {
                            return new JumplinePlugin($this->tinyMce());
                        }
                    );
                }
            }
        );

        $this->tinyMce()->event()->on(
            'tiny-mce.plugin.booting',
            function (string $alias, PluginDriverInterface $plugin) {
                $plugin->params([
                    /**
                     * @var bool $admin_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères dans l'interface d'administration (bouton).
                     */
                    'admin_enqueue_scripts' => true,
                    /**
                     * @var bool $plugin_enqueue_css Activation de la mise en file automatique des styles du plugin.
                     */
                    'plugin_enqueue_css'    => true,
                    /**
                     * @var bool $plugin_enqueue_js Activation de la mise en file automatique des scripts du plugin.
                     */
                    'plugin_enqueue_js'     => true,
                    /**
                     * @var bool $wp_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères.
                     */
                    'wp_enqueue_scripts'    => false,
                ]);

                add_action(
                    'admin_enqueue_scripts',
                    function () use ($alias, $plugin) {
                        if ($plugin->params('admin_enqueue_scripts')) {
                            if ($cssSrc = $plugin->getEditorCssSrc()) {
                                wp_enqueue_style(md5('editor-styles' . $alias), $cssSrc);
                            }
                            if ($jsSrc = $plugin->getEditorJsSrc()) {
                                wp_enqueue_script(md5('editor-scripts' . $alias), $jsSrc);
                            }
                        }
                    }
                );

                add_action(
                    'wp_enqueue_scripts',
                    function () use ($alias, $plugin) {
                        if ($plugin->params('admin_enqueue_scripts')) {
                            if ($cssSrc = $plugin->getThemeCssSrc()) {
                                wp_enqueue_style(md5('theme-styles' . $alias), $cssSrc);
                            }
                            if ($jsSrc = $plugin->getThemeJsSrc()) {
                                wp_enqueue_script(md5('theme-scripts' . $alias), $jsSrc);
                            }
                        }
                    }
                );
            }
        );

        add_action(
            'init',
            function () {
                if ($this->userCan()) {
                    $this->tinyMce()->loadPlugins();

                    foreach ($this->tinyMce()->getPlugins() as $alias => $plugin) {
                        add_filter(
                            'mce_external_plugins',
                            function (array $externalPlugins = []) use ($alias, $plugin) {
                                if ($plugin->params('plugin_enqueue_js') && ($jsSrc = $plugin->getJsSrc())) {
                                    $externalPlugins[$alias] = $jsSrc;
                                }
                                return $externalPlugins;
                            }
                        );

                        add_filter(
                            'mce_css',
                            function (string $mce_css) use ($alias, $plugin) {
                                if ($plugin->params('plugin_enqueue_css') && ($cssSrc = $plugin->getCssSrc())) {
                                    $mce_css .= ', ' . $cssSrc;
                                }
                                return $mce_css;
                            }
                        );
                    }

                    add_filter(
                        'tiny_mce_before_init',
                        function (array $mceInit) {
                            foreach ($this->tinyMce()->config('init', []) as $key => $value) {
                                switch ($key) {
                                    default :
                                        $mceInit[$key] = is_array($value) ? json_encode($value) : (string)$value;
                                        break;
                                    case 'toolbar' :
                                        break;
                                    case 'toolbar1' :
                                    case 'toolbar2' :
                                    case 'toolbar3' :
                                    case 'toolbar4' :
                                        $mceInit[$key] = $value;
                                        $this->tinyMce()->fetchToolbarButtons($value);
                                        break;
                                }
                            }

                            foreach ($this->tinyMce()->getMceInit() as $key => $value) {
                                $mceInit[$key] = is_array($value) ? json_encode($value) : (string)$value;
                            }

                            foreach (array_keys($this->tinyMce()->getPlugins()) as $alias) {
                                if (!$this->tinyMce()->hasButton($alias)) {
                                    $mceInit['toolbar3'] = $mceInit['toolbar3'] ?? '';
                                    $mceInit['toolbar3'] .= ' ' . $alias;
                                }
                            }
                            return $mceInit;
                        }
                    );
                }
            },
            0
        );
    }

    /**
     * Vérification des habilitations utilisateur.
     *
     * @param WP_User|null $wp_user
     *
     * @return bool
     */
    public function userCan(?WP_User $wp_user = null): bool
    {
        if ($wp_user === null) {
            $wp_user = wp_get_current_user();
        }

        return ($wp_user->has_cap('edit_posts') || $wp_user->has_cap('edit_pages')) && get_user_option('rich_editing');
    }
}
