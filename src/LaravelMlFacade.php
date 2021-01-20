<?php

namespace LaravelMl;

use Illuminate\Support\Facades\Facade;

class LaravelMlFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-ml';
    }
}
