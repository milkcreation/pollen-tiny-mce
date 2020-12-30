<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\TinyMceAwareTrait;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;

abstract class AbstractPlugin implements PluginInterface
{
    use BootableTrait;
    use ParamsBagTrait;
    use TinyMceAwareTrait;

    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = '';

    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
       $this->setTinyMce($tinyMceManager);
    }

    /**
     * @inheritDoc
     */
    public function boot(): PluginInterface
    {
        if (!$this->isBooted()) {
            $this->setBooted();
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->tinyMce()->getPluginUrl($this->getName());
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function parseParams()
    {
        if ($mce_init = $this->params('mce_init', [])) {
            $this->tinyMce()->setAdditionnalConfig($mce_init);
        }
    }
}