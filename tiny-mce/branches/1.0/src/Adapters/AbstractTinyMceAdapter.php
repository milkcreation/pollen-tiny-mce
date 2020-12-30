<?php

declare(strict_types=1);

namespace Pollen\TinyMce\Adapters;

use Pollen\TinyMce\Contracts\TinyMceContract;
use Pollen\TinyMce\TinyMceAwareTrait;

abstract class AbstractTinyMceAdapter implements AdapterInterface
{
    use TinyMceAwareTrait;

    /**
     * @param TinyMceContract $tinyMceManager
     */
    public function __construct(TinyMceContract $tinyMceManager)
    {
        $this->setTinyMce($tinyMceManager);
    }
}
