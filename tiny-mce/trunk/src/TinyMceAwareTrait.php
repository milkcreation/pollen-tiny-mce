<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Exception;
use Pollen\TinyMce\Contracts\TinyMceContract;

trait TinyMceAwareTrait
{
    /**
     * Instance du gestionnaire.
     * @var TinyMceContract|null
     */
    private $tinyMce;

    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return TinyMceContract|null
     */
    public function tinyMce(): ?TinyMceContract
    {
        if (is_null($this->tinyMce)) {
            try {
                $this->tinyMce = TinyMce::instance();
            } catch (Exception $e) {
                $this->tinyMce;
            }
        }
        return $this->tinyMce;
    }

    /**
     * Définition de l'instance du gestionnaire.
     *
     * @param TinyMceContract $tinyMce
     *
     * @return static
     */
    public function setTinyMce(TinyMceContract $tinyMce): self
    {
        $this->tinyMce = $tinyMce;

        return $this;
    }
}