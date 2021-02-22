<?php


namespace LaravelMl;


use LaravelMl\Api\ApiFacade;
use LaravelMl\Api\RecommendationRequest;

trait LmlRecord
{
    /**
     * Hook
     */
    public static function bootLmlRecord(): void
    {
        static::observe(app(LmlRecordObserver::class));
    }

    /**
     * @return array Array of values that make up this sample.
     */
    abstract public function features(): array;

    /**
     * @return mixed A known label. This is either a string for 'categorical' or numeric for 'continuous'.
     */
    abstract public function label();

    abstract protected function config(LmlRecordConfig $config);

    /**
     * @return string unique model name
     */
    public function ml(): LmlRecordConfig {
        $config = LmlRecordConfig::make($this);

        $this->config($config);

        return $config;
    }

    /**
     * @return mixed
     */
    public function predict()
    {
        $response = $this->predictRaw();

        return $response['data'][0];
    }

    /**
     * @param $record
     * @param float $weight
     * @param string|null $description
     * @return mixed
     */
    public function associate($record, float $weight, string $description = null)
    {
        return ApiFacade::associate($this->ml(), $record->ml(), $weight, $description);
    }

    /**
     * @param $record
     * @param float $weight
     * @param string|null $description
     * @return mixed
     */
    public function recommend($record, RecommendationRequest $request)
    {
        return ApiFacade::recommend($this->ml(), $request);
    }

    /**
     * @return bool
     */
    public function isTrainable()
    {
        return true;
    }

    /**
     * @return array JSON response array
     */
    protected function predictRaw()
    {
        return ApiFacade::predict($this->ml());
    }
}
