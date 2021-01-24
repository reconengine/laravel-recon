<?php


namespace LaravelMl;


trait MlModel
{
    /**
     * Hook
     */
    public static function bootMlModel(): void
    {
        static::observe(app(MlModelObserver::class));
    }

    /**
     * @return array Array of values that make up this sample.
     */
    abstract public function features(): array;

    /**
     * @return mixed A known label. This is either a string for 'categorical' or numeric for 'continuous'.
     */
    abstract public function label();

    /**
     * @return string unique model name
     */
    abstract protected function config(MlModelConfig $config);

    /**
     * @return string unique model name
     */
    public function ml(): MlModelConfig {
        $config = MlModelConfig::make();

        // TODO: gives move control
        $this->config($config);

        if (null === $config->id()) {
            $config->setId($this->id);
        }

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
     * @return bool
     */
    public function isTrainable()
    {
        return true;
    }

    /**
     * @return array
     */
    public function toMlJson()
    {
        return [
            'features' => $this->features(),
            'label' => $this->label(),
            'identifier' => $this->ml()->id(),
        ];
    }

    /**
     * @return array JSON response array
     */
    protected function predictRaw()
    {
        return ApiFacade::predict($this);
    }
}
