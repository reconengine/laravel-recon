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

//
//    /**
//     * @return mixed
//     */
//    public function predict()
//    {
//        $response = $this->predictRaw();
//
//        return $response['data'][0];
//    }
//
//    /**
//     * @param $record
//     * @param float $weight
//     * @param string|null $description
//     * @return mixed
//     */
//    public function associate($record, float $weight, string $description = null)
//    {
//        return ApiFacade::associate($this->ml(), $record->ml(), $weight, $description);
//    }
//
//    /**
//     * @param $record
//     * @param float $weight
//     * @param string|null $description
//     * @return mixed
//     */
//    public function recommend($record, RecommendationRequest $request)
//    {
//        return ApiFacade::recommend($this->ml(), $request);
//    }

    /**
     * @return array JSON response array
     */
//    protected function predictRaw()
//    {
//        return ApiFacade::predict($this->ml());
//    }
}