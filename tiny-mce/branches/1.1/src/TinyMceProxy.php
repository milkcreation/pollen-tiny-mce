<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

use Psr\Container\ContainerInterface as Container;
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
            $container = method_exists($this, 'getContainer') ? $this->getContainer() : null;

            if ($container instanceof Container && $container->has(TinyMceInterface::class)) {
                $this->tinyMce = $container->get(TinyMceInterface::class);
            } else {
                try {
                    $this->tinyMce = TinyMce::getInstance();
                } catch(RuntimeException $e) {
                    $this->tinyMce = new TinyMce();
                }
            }
        }

        return $this->tinyMce;
    }

    /**
     * Définition de l'instance du gestionnaire de tinyMCE.
     *
     * @param TinyMceInterface $tinyMce
     *
     * @return static
     */
    public function setTinyMce(TinyMceInterface $tinyMce): self
    {
        $this->tinyMce = $tinyMce;

        return $this;
    }
}