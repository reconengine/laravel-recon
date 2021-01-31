<?php


namespace LaravelMl;


use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\Exceptions\LmlConfigValidationException;

class LmlDatabaseConfig
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

    /**
     * MlModelConfig constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return static
     */
    public static function make()
    {
        return new static;
    }

    /**
     * @param mixed $type
     * @return LmlDatabaseConfig
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
     * @return LmlDatabaseConfig
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
     * @return LmlDatabaseConfig
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
     * @return array
     */
    public function toJson()
    {
        return [
            'type' => $this->type,
            'datatype' => $this->datatype,
            'name' => $this->name,
        ];
    }

    /**
     * @throws DatatypeMismatchException
     */
    public function validate()
    {
        // TODO: use Laravel validation to assert these are all set correctly.
        if (null === $this->type) {
            throw new LmlConfigValidationException("'type' is required");
        }
        if (null === $this->datatype) {
            throw new LmlConfigValidationException("'datatype' is required");
        }
        if (null === $this->name) {
            throw new LmlConfigValidationException("'name' is required");
        }

        $supportedTypes = self::SUPPORTED_DATATYPES[$this->type];

        if (! in_array($this->datatype, $supportedTypes)) {
            $supportedString = collect($supportedTypes)->join(', ');
            throw new DatatypeMismatchException("Datatype is currently not supported for model type: {$this->type}. Supported: {$supportedString}");
        }
    }
}
