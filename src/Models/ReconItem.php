<?php


namespace Recon\Models;


use Recon\Api\ApiFacade;
use Recon\Observers\ReconItemObserver;

trait ReconItem
{
    use ReconModel;

    /**
     * Hook
     */
    public static function bootReconItem()
    {
        static::observe(ReconItemObserver::class);
    }

    /**
     * @return mixed
     */
    public function related()
    {
        return ApiFacade::getRelatedItems($this);
    }
}
