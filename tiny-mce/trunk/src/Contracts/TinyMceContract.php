<?php declare(strict_types=1);

namespace Pollen\TinyMce\Contracts;

use Pollen\TinyMce\Adapters\AdapterInterface;
use Pollen\TinyMce\Plugins\PluginInterface;
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
     * Récupération de l'instance de l'adapteur.
     *
     * @return AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface;

    /**
     * Récupération de l'url vers les assets d'un plugin.
     *
     * @param string $name Nom de qualification du plugin.
     *
     * @return string
     */
    public function getPluginAssetsUrl(string $name): string;

    /**
     * Récupération de l'url vers le scripts d'un plugin.
     *
     * @param string $name Nom de qualification du plugin.
     *
     * @return string
     */
    public function getPluginUrl(string $name): string;

    /**
     * Récupération de la liste des boutons de plugins externes déclarés dans la configuration.
     *
     * @param string $buttons Liste des boutons définis dans la configuration.
     *
     * @return void
     */
    public function fetchPluginsButtons($buttons = ''): void;

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
     * Définition des attributs de configuration additionnels.
     *
     * @param array $config Liste des attributs de configuration additionnels.
     *
     * @return static
     */
    public function setAdditionnalConfig(array $config): TinyMceContract;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): TinyMceContract;

    /**
     * Déclaration d'un plugin.
     *
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function setPlugin(PluginInterface $plugin): TinyMceContract;
}
