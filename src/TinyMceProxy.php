<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Pollen\Support\StaticProxy;
use RuntimeException;

trait TinyMceProxy
{
    /**
     * Instance du gestionnaire de tinyMCE.
     * @var TinyMceInterface
     */
    private $tinyMce;

    /**
     * Instance du gestionnaire de tinyMCE.
     *
     * @return TinyMceInterface
     */
    public function tinyMce(): TinyMceInterface
    {
        if ($this->tinyMce === null) {
            try {
                $this->tinyMce = TinyMce::getInstance();
            } catch (RuntimeException $e) {
                $this->tinyMce = StaticProxy::getProxyInstance(
                    TinyMceInterface::class,
                    TinyMce::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        return $this->tinyMce;
    }

    /**
     * Définition de l'instance du gestionnaire de tinyMCE.
     *
     * @param TinyMceInterface $tinyMce
     *
     * @return void
     */
    public function setTinyMce(TinyMceInterface $tinyMce): void
    {
        $this->tinyMce = $tinyMce;
    }
}