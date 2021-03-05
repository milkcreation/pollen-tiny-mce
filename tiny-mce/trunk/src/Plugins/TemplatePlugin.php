<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Plugins;

use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\PluginDriver;

class TemplatePlugin extends PluginDriver
{
    /**
     * Liste des templates déclarés.
     * @var array
     */
    protected $templates = [];

    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
        parent::__construct($tinyMceManager);

        $baseurl = $this->tinyMce()->resources()->url('/views/plugins/template');

        $this->templates = [
            [
                'title'       => '2 Colonnes : 1/4, 3/4',
                'description' => '1 colonne d\'1/4 et l\'autre de 3/4',
                'url'         => $baseurl . '/2cols_0.25-0.75.htm',
            ],
            [
                'title'       => '2 Colonnes : 1/3, 2/3',
                'description' => '1 colonne d\'1/3 et l\'autre de 2/3',
                'url'         => $baseurl . '/2cols_0.33-0.66.htm',
            ],
            [
                'title'       => '2 Colonnes : 1/2, 1/2',
                'description' => '1 colonnes d\'1/2 et l\'autre d\'1/2',
                'url'         => $baseurl . '/2cols_0.5-0.5.htm',
            ],
            [
                'title'       => '2 Colonnes : 2/3, 1/3',
                'description' => '1 colonne de 2/3 et l\'autre d\'1/3',
                'url'         => $baseurl . '/2cols_0.66-0.33.htm',
            ],
            [
                'title'       => '2 Colonnes : 3/4, 1/4',
                'description' => '1 colonne de 3/4 et l\'autre d\'1/4',
                'url'         => $baseurl . '/2cols_0.75-0.25.htm',
            ],
            [
                'title'       => '3 Colonnes : 1/4, 1/4, 1/2',
                'description' => '1 colonne d\'1/4, une d\'1/4 et une d\'1/2',
                'url'         => $baseurl . '/3cols_0.25-0.25-0.5.htm',
            ],
            [
                'title'       => '3 Colonnes : 1/4, 1/2, 1/4',
                'description' => '1 colonne d\'1/4, une d\'1/2 et une d\'1/4',
                'url'         => $baseurl . '/3cols_0.25-0.5-0.25.htm',
            ],
            [
                'title'       => '3 Colonnes : 1/3, 1/3, 1/3',
                'description' => '1 colonne d\'1/3, une d\'1/3 et une d\'1/3',
                'url'         => $baseurl . '/3cols_0.33-0.33-0.33.htm',
            ],
            [
                'title'       => '3 Colonnes : 1/2, 1/4, 1/4',
                'description' => '1 colonne d\'1/2, une d\'1/4 et une d\'1/4',
                'url'         => $baseurl . '/3cols_0.5-0.25-0.25.htm',
            ],
            [
                'title'       => '4 Colonnes : 1/4, 1/4, 1/4, 1/4',
                'description' => '1 colonnes d\'1/4, une d\'1/4, une d\'1/4 et une d\'1/4',
                'url'         => $baseurl . '/4cols_0.25-0.25-0.25-0.25.htm',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'mce_init' => [
                    'templates' => $this->getXhrUrl()
                ],
            ]
        );
    }

    /**
     * Récupération de la liste des templates déclarés.
     *
     * @return array
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * Défintion d'un template.
     *
     * @param string $title
     * @param string $description
     * @param string $url
     *
     * @return static
     */
    public function setTemplate(string $title, string $description, string $url): self
    {
        $this->templates[] = compact('title', 'description', 'url');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        return $this->getTemplates();
    }
}
