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
    abstract protected function features(): array;

    /**
     * @return mixed A known label. This is either a string for 'categorical' or numeric for 'continuous'.
     */
    abstract protected function label();

    /**
     * @return string unique model name
     */
    abstract public function ml(): MlModelConfig;

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
}
