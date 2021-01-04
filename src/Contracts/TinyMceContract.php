<?php declare(strict_types=1);

namespace Pollen\TinyMce\Contracts;

use League\Route\Http\Exception\NotFoundException;
use Pollen\TinyMce\Adapters\AdapterInterface;
use Pollen\TinyMce\PluginDriverInterface;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ContainerAwareTrait
 */
interface TinyMceContract
{
    /**
     * Récupération de l'instance.
     *
     * @return static
     */
    public static function instance(): TinyMceContract;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): TinyMceContract;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return ParamsBag|int|string|array|object
     */
    public function config($key = null, $default = null);

    /**
     * Récupération de la liste des boutons de plugins externes déclarés dans la configuration.
     *
     * @param string $buttonsDefinition
     *
     * @return static
     */
    public function fetchToolbarButtons(string $buttonsDefinition): TinyMceContract;

    /**
     * Récupération de l'instance de l'adapteur.
     *
     * @return AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface;

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
     * @return string
     */
    public function getXhrRouteUrl(string $plugin, ?string $controller = null, array $params = []): string;

    /**
     * Vérification d'existance d'un bouton déclaré.
     *
     * @param string $button
     *
     * @return bool
     */
    public function hasButton(string $button): bool;

    /**
     * Vérification d'existance d'un plugin déclaré.
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
    public function loadPlugins(): TinyMceContract;

    /**
     * Déclaration d'un plugin par défaut.
     *
     * @param string $alias
     * @param PluginDriverInterface|string $pluginDefinition
     *
     * @return static
     */
    public function registerDefaultPlugin(string $alias, $pluginDefinition): TinyMceContract;

    /**
     * Déclaration d'un plugin.
     *
     * @param string $alias
     * @param PluginDriverInterface|string $pluginDefinition
     *
     * @return static
     */
    public function registerPlugin(string $alias, $pluginDefinition): TinyMceContract;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return LocalFilesystem|string|null
     */
    public function resources(?string $path = null);

    /**
     * Définition de l'adapteur associé.
     *
     * @param AdapterInterface $adapter
     *
     * @return static
     */
    public function setAdapter(AdapterInterface $adapter): TinyMceContract;

    /**
     * Définition des paramètres de configuration généraux de tinyMCE.
     *
     * @param array $mceInit
     *
     * @return static
     */
    public function setMceInit(array $mceInit): TinyMceContract;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): TinyMceContract;

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
