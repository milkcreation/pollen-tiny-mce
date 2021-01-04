<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters\Wordpress\Plugins;

use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\PluginDriver;
use tiFy\Support\Proxy\Asset;

class JumplinePlugin extends PluginDriver
{
    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
        parent::__construct($tinyMceManager);

        add_action(
            'admin_enqueue_scripts',
            function () {
                Asset::setInlineJs(
                    "let tiFyTinyMCEJumpLinel10n={'title':'" . __('Saut de ligne', 'tify') . "'};",
                    true
                );
                Asset::setInlineCss("i.mce-i-jumpline::before{content:'\\f474';font-family:'dashicons';}");
            }
        );
    }
}
