<?php


namespace Recon;


use Recon\Api\ApiFacade;
use Recon\Observers\ReconUserObserver;

trait ReconUser
{
    use ReconModel;

    /**
     * Hook
     */
    public static function bootReconUser()
    {
        static::observe(ReconUserObserver::class);
    }

    /**
     * @return mixed
     */
    public function recommend()
    {
        return ApiFacade::getUserRecommendations($this);
    }
}
