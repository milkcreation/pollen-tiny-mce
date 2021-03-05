<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters\Wordpress\Plugins;

use Pollen\TinyMce\PluginDriver;
use Pollen\TinyMce\TinyMceInterface;

class JumplinePlugin extends PluginDriver
{
    /**
     * @param TinyMceInterface $tinyMce
     */
    public function __construct(TinyMceInterface $tinyMce)
    {
        parent::__construct($tinyMce);

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
