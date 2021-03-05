<?php

declare(strict_types=1);

namespace Pollen\TinyMce;

abstract class AbstractTinyMceAdapter implements TinymceAdapterInterface
{
    use TinyMceProxy;

    /**
     * @param TinyMceInterface $tinyMce
     */
    public function __construct(TinyMceInterface $tinyMce)
    {
        $this->setTinyMce($tinyMce);
    }
}
