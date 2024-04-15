<?php

declare(strict_types=1);

namespace Yard\Models\Facades;

use Illuminate\Support\Facades\Facade;

class Model extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Model';
    }
}
