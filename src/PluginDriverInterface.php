<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

/**
 * @mixin \Pollen\TinyMce\TinyMceAwareTrait
 * @mixin \tiFy\Support\Concerns\BootableTrait;
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait;
 */
interface PluginDriverInterface
{
    /**
     * Initialisation du controleur de plugin
     *
     * @return static
     */
    public function boot(): PluginDriverInterface;

    /**
     * Récupération de l'alias de qualification du plugin.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de la source vers les styles CSS de l'éditeur
     * @internal Apparence des boutons, modales ...
     *
     * @return string|null
     */
    public function getEditorCssSrc(): ?string;

    /**
     * Récupération de la source vers les scripts JS de l'éditeur
     * @internal Actions des boutons, modales ...
     *
     * @return string|null
     */
    public function getEditorJsSrc(): ?string;

    /**
     * Récupération de la source vers les styles CSS du plugin.
     *
     * @return string|null
     */
    public function getCssSrc(): ?string;

    /**
     * Récupération de la source vers les scripts JS du plugin.
     *
     * @return string|null
     */
    public function getJsSrc(): ?string;

    /**
     * Récupération de la source vers les styles CSS du theme.
     * @internal Apparence du front-end
     *
     * @return string|null
     */
    public function getThemeCssSrc(): ?string;

    /**
     * Récupération de la source vers les scripts JS du theme.
     * @internal Actions du front-end
     *
     * @return string|null
     */
    public function getThemeJsSrc(): ?string;

    /**
     * Récupération de l'url de traitement des requêtes XHR.
     *
     * @param array $params
     *
     * @return string
     */
    public function getXhrUrl(array $params = []): string;

    /**
     * Définition de l'alias de qualification du plugin.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): PluginDriverInterface;

    /**
     * Contrôleur de traitement des requêtes XHR.
     *
     * @param array ...$args
     *
     * @return array
     */
    public function xhrResponse(...$args): array;
}