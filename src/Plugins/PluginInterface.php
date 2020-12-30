<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

/**
 * @mixin \Pollen\TinyMce\TinyMceAwareTrait
 * @mixin \tiFy\Support\Concerns\BootableTrait;
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait;
 */
interface PluginInterface
{
    /**
     * Initialisation du controleur de plugin
     *
     * @return static
     */
    public function boot(): PluginInterface;

    /**
     * Récupération de l'identifiant de qualification du plugin.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de l'url vers le script JS du plugin.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Vérification du status d'activité du plugin.
     *
     * @return bool
     */
    public function isEnabled(): bool;
}