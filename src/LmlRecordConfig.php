<?php


namespace LaravelMl;


use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\Exceptions\LmlConfigValidationException;
use ReflectionClass;

class LmlRecordConfig
{
    /**
     * @var LmlDatabaseConfig $database
     */
    protected $database = null;

    protected $id;
    protected $class;
    protected $features;
    protected $label;

    /**
     * MlModelConfig constructor.
     */
    public function __construct($model)
    {
        $this->id = $model->id;
        $reflect = new ReflectionClass($model);
        $this->class = $reflect->getShortName();

        $this->features = $model->features();
        $this->label = $model->label();
    }

    /**
     * @return static
     */
    public static function make($model)
    {
        return new static($model);
    }

    /**
     * @param string $name
     * @return LmlRecordConfig
     */
    public function setDatabase(string $database)
    {
        $databaseConfig = collect(config('laravel-ml.databases'))->first(function (LmlDatabaseConfig $config) use ($database) {
            return $database === $config->name();
        });

        if (! $databaseConfig) {
            throw new LmlConfigValidationException("No database configuration with the name: '{$database}'.");
        }

        $this->database = $databaseConfig;
        return $this;
    }

    /**
     * @return mixed
     */
    public function database()
    {
        return $this->database;
    }

    /**
     * @return mixed
     */
    public function features()
    {
        return $this->features;
    }

    /**
     * @return string
     */
    public function networkId()
    {
        return "{$this->class}:{$this->id}";
    }

    /**
     * @return array
     */
    public function toJson()
    {
        return [
            'features' => $this->features,
            'label' => $this->label,
            'identifier' => $this->id,
            'class' => $this->class,
        ];
    }

    /**
     * @param LmlRecord $model
     * @throws DatatypeMismatchException
     */
    public function validate()
    {
        $this->database()->validate();

        if (null === $this->database) {
            throw new LmlConfigValidationException("'database' is required");
        }
        if (null === $this->id) {
            throw new LmlConfigValidationException("'id' is required");
        }
        if (null === $this->class) {
            throw new LmlConfigValidationException("'class' is required");
        }

        $this->validateLabel($this->label);
        $this->validateFeatures($this->features);
    }

    /**
     * @param $label
     * @throws DatatypeMismatchException
     */
    public function validateLabel($label)
    {
        if ($this->database->type() !== LmlDatabaseConfig::TYPE_ANOMALY) {
            $detectedDatatypes = $this->detectDatatypes([$label]);
            if ([$this->database->type()] !== $detectedDatatypes) {
                $errorString = collect($detectedDatatypes)->join(', ');
                throw new DatatypeMismatchException("Model type mismatch. Allowed: {$this->database->type()}. Found: {$errorString}");
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
        if ([$this->database->datatype()] !== $detectedDatatypes) {
            $errorString = collect($detectedDatatypes)->join(', ');
            throw new DatatypeMismatchException("Model datatype mismatch. Allowed: {$this->database->datatype()}. Found: {$errorString}");
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
                    array_push($detectedDatatypes, LmlDatabaseConfig::DATATYPE_CONTINUOUS);
                    break;
                case 'string':
                    array_push($detectedDatatypes, LmlDatabaseConfig::DATATYPE_CATEGORICAL);
                    break;
                default:
                    array_push($detectedDatatypes, 'other');
                    break;
            }
        }

        return array_unique($detectedDatatypes);
    }
}
