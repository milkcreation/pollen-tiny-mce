<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters;

use Pollen\TinyMce\Contracts\TinyMceContract;

class WordpressAdapter extends AbstractTinyMceAdapter
{
    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
        parent::__construct($tinyMceManager);

        add_action('init', function () {
            foreach (config('tiny-mce.plugins', []) as $name => $attrs) {

                if (is_numeric($name)) {
                    $name  = (string)$attrs;
                    $attrs = [];
                }

                if ($this->tinyMce()->containerHas("tiny-mce.plugins.{$name}")) {
                    $this->tinyMce()->containerGet("tiny-mce.plugins.{$name}", [$name, $attrs]);
                }
            }
        }, 0);


        add_filter('mce_external_plugins', function ($externalPlugins = []) {
            foreach ($this->externalPlugins as $name => $plugin) {
                $externalPlugins[$name] = $plugin->getUrl();
            }

            return $externalPlugins;
        });

        add_filter('tiny_mce_before_init', function ($mceInit) {
            foreach (config('tiny-mce.init', []) as $key => $value) {
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
                        $this->getExternalPluginsButtons($value);
                        break;
                }
            }

            foreach ($this->additionnalConfig as $key => $value) {
                $mceInit[$key] = is_array($value) ? json_encode($value) : (string)$value;
            }

            foreach (array_keys($this->externalPlugins) as $name) {
                if (!in_array($name, $this->toolbarButtons)) {
                    if (!empty($mceInit['toolbar3'])) {
                        $mceInit['toolbar3'] .= ' ' . $name;
                    } else {
                        $mceInit['toolbar3'] = $name;
                    }
                }
            }
            return $mceInit;
        });
    }
}
