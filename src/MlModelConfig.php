<?php


namespace LaravelMl;


class MlModelConfig
{
    const TYPE_CATEGORICAL = 'categorical';
    const TYPE_CONTINUOUS = 'continuous';

    const TYPES = [
        self::TYPE_CATEGORICAL,
        self::TYPE_CONTINUOUS,
    ];

    protected $name;
    protected $type;
    protected $id;

    public function __construct()
    {
    }

    public static function make()
    {
        return new static;
    }

    /**
     * @param mixed $type
     * @return MlModelConfig
     */
    public function setType($type)
    {
        if (! in_array($type, self::TYPES)) {
            throw new \Exception('Unsupported model type. Options are: ' . collect(self::TYPES)->join(', '));
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @param mixed $name
     * @return MlModelConfig
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @param mixed $id
     * @return MlModelConfig
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }
}
