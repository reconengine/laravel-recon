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

    /**
     * MlModelConfig constructor.
     * @param $model
     */
    public function __construct()
    {
    }

    /**
     * @param $model
     * @return static
     */
    public static function make()
    {
        return new static();
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
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => $this->type,
            'datatype' => $this->datatype,
            'name' => $this->name,
            'identifier' => $this->id,
        ];
    }

    /**
     * @throws DatatypeMismatchException
     */
    public function validateConfig()
    {
        // TODO: use Laravel validation to assert these are all set correctly.
        if (null === $this->type) {
            throw new MlConfigValidationException("'type' is required");
        }
        if (null === $this->datatype) {
            throw new MlConfigValidationException("'datatype' is required");
        }
//        ID not required for the model
//        if ($isIdRequired && null === $this->id) {
//            throw new MlConfigValidationException("'id' is required");
//        }
        if (null === $this->name) {
            throw new MlConfigValidationException("'name' is required");
        }

        $supportedTypes = self::SUPPORTED_DATATYPES[$this->type];

        if (! in_array($this->datatype, $supportedTypes)) {
            $supportedString = collect($supportedTypes)->join(', ');
            throw new DatatypeMismatchException("Datatype is currently not supported for model type: {$this->type}. Supported: {$supportedString}");
        }
    }


    /**
     * @throws DatatypeMismatchException
     */
    public function validateItem()
    {
        $this->validateConfig();

        if (null === $this->id) {
            throw new MlConfigValidationException("'id' is required");
        }
    }

    /**
     * @param MlModel $model
     * @throws DatatypeMismatchException
     */
    public function validateData($model)
    {
        $this->validateLabel($model->label());
        $this->validateFeatures($model->features());
    }

    /**
     * @param $label
     * @throws DatatypeMismatchException
     */
    public function validateLabel($label)
    {
        if ($this->type !== MlModelConfig::TYPE_ANOMALY) {
            $detectedDatatypes = $this->detectDatatypes([$label]);
            if ([$this->type] !== $detectedDatatypes) {
                $errorString = collect($detectedDatatypes)->join(', ');
                throw new DatatypeMismatchException("Model type mismatch. Allowed: {$this->type}. Found: {$errorString}");
            }
        }
    }

    /**
     * @param array $features
     * @throws DatatypeMismatchException
     */
    public function validateFeatures(array $features)
    {
        $detectedDatatypes = $this->detectDatatypes($features);
        if ([$this->datatype] !== $detectedDatatypes) {
            $errorString = collect($detectedDatatypes)->join(', ');
            throw new DatatypeMismatchException("Model datatype mismatch. Allowed: {$this->datatype}. Found: {$errorString}");
        }
    }

    /**
     * @return array
     */
    protected function detectDatatypes(array $data)
    {
        $detectedDatatypes = [];

        foreach ($data as $i => $value) {
            switch (gettype($value)) {
                case 'double':
                case 'integer':
                    array_push($detectedDatatypes, MlModelConfig::DATATYPE_CONTINUOUS);
                    break;
                case 'string':
                    array_push($detectedDatatypes, MlModelConfig::DATATYPE_CATEGORICAL);
                    break;
                default:
                    array_push($detectedDatatypes, 'other');
                    break;
            }
        }

        return array_unique($detectedDatatypes);
    }
}
