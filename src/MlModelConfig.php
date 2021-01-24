<?php


namespace LaravelMl;


use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\Exceptions\MlConfigValidationException;

class MlModelConfig
{
    const TYPE_CATEGORICAL = 'categorical';
    const TYPE_CONTINUOUS = 'continuous';
    const TYPE_ANOMALY = 'anomaly';

    const DATATYPE_CATEGORICAL = 'categorical';
    const DATATYPE_CONTINUOUS = 'continuous';

    const TYPES = [
        self::TYPE_CATEGORICAL,
        self::TYPE_CONTINUOUS,
        self::TYPE_ANOMALY,
    ];
    const DATATYPES = [
        self::DATATYPE_CATEGORICAL,
        self::DATATYPE_CONTINUOUS,
    ];

    const SUPPORTED_DATATYPES = [
        self::TYPE_CATEGORICAL => [
            self::DATATYPE_CONTINUOUS,
            self::DATATYPE_CATEGORICAL,
        ],
        self::TYPE_CONTINUOUS => [
            self::DATATYPE_CONTINUOUS,
            self::DATATYPE_CATEGORICAL,
        ],
        self::TYPE_ANOMALY => [
            self::DATATYPE_CONTINUOUS,
        ],
    ];

    protected $name;
    protected $type;
    protected $datatype;
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
    public function setType(string $type)
    {
        if (! in_array($type, self::TYPES)) {
            throw new \Exception('Unsupported model type. Options are: ' . collect(self::TYPES)->join(', '));
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @param mixed $datatype
     * @return MlModelConfig
     */
    public function setDatatype(string $datatype)
    {
        if (! in_array($datatype, self::DATATYPES)) {
            throw new \Exception('Unsupported model type. Options are: ' . collect(self::DATATYPES)->join(', '));
        }

        $this->datatype = $datatype;
        return $this;
    }

    /**
     * @return mixed
     */
    public function datatype()
    {
        return $this->datatype;
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
     * @param mixed $id
     * @return MlModelConfig
     */
    public function setId($id)
    {
        $this->id = strval($id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @throws DatatypeMismatchException
     */
    public function validate()
    {
        // TODO: use Laravel validation to assert these are all set correctly.
        if (! $this->type) {
            throw new MlConfigValidationException("'type' is required");
        }
        if (! $this->datatype) {
            throw new MlConfigValidationException("'datatype' is required");
        }
        if (! $this->id) {
            throw new MlConfigValidationException("'id' is required");
        }
        if (! $this->name) {
            throw new MlConfigValidationException("'name' is required");
        }

        $supportedTypes = self::SUPPORTED_DATATYPES[$this->type];

        if (! in_array($this->datatype, $supportedTypes)) {
            $supportedString = collect($supportedTypes)->join(', ');
            throw new DatatypeMismatchException("Datatype is currently not supported for model type: {$this->type}. Supported: {$supportedString}");
        }
    }
}
