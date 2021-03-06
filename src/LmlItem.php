<?php


namespace LaravelMl;


use LaravelMl\Api\ApiFacade;
use LaravelMl\Helpers\SchemaDefinition;
use LaravelMl\Observers\LmlItemObserver;

trait LmlItem
{
    use LmlModel;

    /**
     * Hook
     */
    public static function bootLmlItem()
    {
        static::observe(LmlItemObserver::class);
    }

    /**
     * @return mixed
     */
    public function related()
    {
        return ApiFacade::getRelatedItems($this);
    }
}
