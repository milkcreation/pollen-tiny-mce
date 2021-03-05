<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use League\Route\Http\Exception\NotFoundException;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\EventProxyInterface;
use Pollen\Support\Proxy\RouterProxyInterface;

interface TinyMceInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ContainerProxyInterface,
    EventProxyInterface,
    RouterProxyInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot():TinyMceInterface;

    /**
     * Récupération de la liste des boutons de plugins externes déclarés dans la configuration.
     *
     * @param string $buttonsDefinition
     *
     * @return static
     */
    public function fetchToolbarButtons(string $buttonsDefinition): TinyMceInterface;

    /**
     * Récupération de l'instance de l'adapteur.
     *
     * @return TinyMceAdapterInterface|null
     */
    public function getAdapter(): ?TinyMceAdapterInterface;

    /**
     * Récupération de la liste des paramètres généraux de tinyMce.
     *
     * @return array
     */
    public function getMceInit(): array;

    /**
     * Récupération de l'instance d'un plugin.
     *
     * @param string $alias
     * @param array|null $params
     *
     * @return PluginDriverInterface|null
     */
    public function getPlugin(string $alias, array $params = []): ?PluginDriverInterface;

    /**
     * Récupération de la liste des plugins déclarés.
     *
     * @return PluginDriverInterface[]|array
     */
    public function getPlugins(): array;

    /**
     * Récupération de l'url de traitement des requêtes XHR.
     *
     * @param string $plugin Alias de qualification du pilote associé.
     * @param string|null $controller Nom de qualification du controleur de traitement de la requête XHR.
     * @param array $params Liste de paramètres complémentaire transmis dans l'url
     *
     * @return string|null
     */
    public function getXhrRouteUrl(string $plugin, ?string $controller = null, array $params = []): ?string;

    /**
     * Vérification d'existance d'un bouton déclaré.
     *
     * @param string $button
     *
     * @return bool
     */
    public function hasButton(string $button): bool;

    /**
     * Vérification d'existence d'un plugin déclaré.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function hasPlugin(string $alias): bool;

    /**
     * Chargement des plugins.
     *
     * @return static
     */
    public function loadPlugins(): TinyMceInterface;

    /**
     * Déclaration d'un plugin par défaut.
     *
     * @param string $alias
     * @param PluginDriverInterface|string $pluginDefinition
     *
     * @return static
     */
    public function registerDefaultPlugin(string $alias, $pluginDefinition): TinyMceInterface;

    /**
     * Déclaration d'un plugin.
     *
     * @param string $alias
     * @param PluginDriverInterface|string $pluginDefinition
     *
     * @return static
     */
    public function registerPlugin(string $alias, $pluginDefinition): TinyMceInterface;

    /**
     * Chemin absolu vers une ressource (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return string
     */
    public function resources(?string $path = null): string;

    /**
     * Définition du chemin absolu vers le répertoire des ressources.
     *
     * @param string $resourceBaseDir
     *
     * @return static
     */
    public function setResourcesBaseDir(string $resourceBaseDir): TinyMceInterface;

    /**
     * Définition de l'adapteur associé.
     *
     * @param TinyMceAdapterInterface $adapter
     *
     * @return static
     */
    public function setAdapter(TinyMceAdapterInterface $adapter): TinyMceInterface;

    /**
     * Définition des paramètres de configuration généraux de tinyMCE.
     *
     * @param array $mceInit
     *
     * @return static
     */
    public function setMceInit(array $mceInit): TinyMceInterface;

    /**
     * Répartiteur de traitement d'une requête XHR.
     *
     * @param string $pluginAlias Alias de qualification du pilote associé.
     * @param string $controller Nom de qualification du controleur de traitement de la requête.
     * @param mixed ...$args Liste des arguments passés au controleur
     *
     * @return array
     *
     * @throws NotFoundException
     */
    public function xhrResponseDispatcher(string $pluginAlias, string $controller, ...$args): array;
}
