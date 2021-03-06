<?php


namespace LaravelMl;


use LaravelMl\Api\ApiFacade;
use LaravelMl\Observers\LmlUserObserver;

trait LmlUser
{
    use LmlModel;

    /**
     * Hook
     */
    public static function bootLmlUser()
    {
        static::observe(LmlUserObserver::class);
    }

    /**
     * @return mixed
     */
    public function recommend()
    {
        return ApiFacade::getUserRecommendations($this);
    }
}
