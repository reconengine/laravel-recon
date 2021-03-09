<?php

namespace Recon;

use Illuminate\Support\Facades\Facade;

class ReconFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'recon';
    }
}
