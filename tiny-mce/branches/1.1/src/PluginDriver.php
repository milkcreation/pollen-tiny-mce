<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Pollen\Http\JsonResponse;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;

abstract class PluginDriver implements PluginDriverInterface
{
    use BootableTrait;
    use ParamsBagAwareTrait;
    use TinyMceProxy;

    /**
     * Alias de qualification du plugin.
     * @var string
     */
    protected $alias = '';

    /**
     * @param TinyMceInterface $tinyMce
     */
    public function __construct(TinyMceInterface $tinyMce)
    {
        $this->setTinyMce($tinyMce);
    }

    /**
     * @inheritDoc
     */
    public function boot(): PluginDriverInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('tiny-mce.plugin.booting', [$this->getAlias(), $this]);

            $this->parseParams();
            $this->setBooted();

            //events()->trigger('tiny-mce.plugin.booted', [$this->getAlias(), $this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * @var array|null $mce_init Paramètres de configuration généraux de tinyMce propre au plugin.
             */
            'mce_init' => null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function getEditorCssSrc(): ?string
    {
        return $this->tinyMce()->resources("/assets/dist/css/plugins/{$this->getAlias()}/editor.css");
    }

    /**
     * @inheritDoc
     */
    public function getEditorJsSrc(): ?string
    {
        return $this->tinyMce()->resources("/assets/dist/js/plugins/{$this->getAlias()}/editor.js");
    }

    /**
     * @inheritDoc
     */
    public function getCssSrc(): ?string
    {
        return $this->tinyMce()->resources("/assets/dist/css/plugins/{$this->getAlias()}/plugin.css");
    }

    /**
     * @inheritDoc
     */
    public function getJsSrc(): ?string
    {
        return $this->tinyMce()->resources("/assets/dist/js/plugins/{$this->getAlias()}/plugin.js");
    }

    /**
     * @inheritDoc
     */
    public function getThemeCssSrc(): ?string
    {
        return $this->tinyMce()->resources("/assets/dist/css/plugins/{$this->getAlias()}/theme.css");
    }

    /**
     * @inheritDoc
     */
    public function getThemeJsSrc(): ?string
    {
        return $this->tinyMce()->resources("/assets/dist/js/plugins/{$this->getAlias()}/theme.js");
    }

    /**
     * @inheritDoc
     */
    public function getXhrUrl(array $params = []): string
    {
        return $this->tinyMce()->getXhrRouteUrl($this->getAlias(), null, $params);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): void
    {
        if ($mceInit = $this->params()->pull('mce_init')) {
            $this->tinyMce()->setMceInit($mceInit);
        }
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): PluginDriverInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): ResponseInterface
    {
        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }
}