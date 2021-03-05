<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

interface TinyMceProxyInterface
{
    /**
     * Instance du gestionnaire de tinyMCE.
     *
     * @return TinyMceInterface
     */
    public function tinyMce(): TinyMceInterface;

    /**
     * Définition de l'instance du gestionnaire de tinyMCE.
     *
     * @param TinyMceInterface $tinyMce
     *
     * @return static|TinyMceProxy
     */
    public function setTinyMce(TinyMceInterface $tinyMce): self;
}