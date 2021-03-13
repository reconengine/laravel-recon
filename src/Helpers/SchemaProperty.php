<?php


namespace Recon\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Arr;
use Recon\Exceptions\ReconConfigValidationException;

class SchemaProperty
{
    public $name;
    public $datatypes = [];

    const DATATYPE_FLOAT = 'float';
    const DATATYPE_DOUBLE = 'double';
    const DATATYPE_INT = 'int';
    const DATATYPE_LONG = 'long';
    const DATATYPE_BOOLEAN = 'boolean';
    const DATATYPE_STRING = 'string';
    const DATATYPE_CATEGORY = 'category';
    const DATATYPE_NULL = 'null';

    const VALID_DATATYPES = [
        self::DATATYPE_FLOAT,
        self::DATATYPE_DOUBLE,
        self::DATATYPE_INT,
        self::DATATYPE_LONG,
        self::DATATYPE_BOOLEAN,
        self::DATATYPE_STRING, // categorical = false
        self::DATATYPE_CATEGORY, // categorical = true
        self::DATATYPE_NULL,
    ];

    /**
     * SchemaProperty constructor.
     * @param $name
     * @param $datatype
     * @throws ReconConfigValidationException
     */
    public function __construct($name, $datatype)
    {
        if (! in_array($datatype, self::VALID_DATATYPES)) {
            throw  new ReconConfigValidationException("{$datatype} is an invalid datatype.");
        }

        $this->name = $name;
        $this->datatypes = Arr::wrap($datatype);
    }

    /**
     * @return $this
     */
    public function nullable()
    {
        array_push($this->datatypes, self::DATATYPE_NULL);

        return $this;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function cast($value)
    {
        $mainDatatype = $this->getMainDatatype();

        switch ($mainDatatype) {
            case self::DATATYPE_FLOAT:
            case self::DATATYPE_DOUBLE: return $this->castToFloat($value);

            case self::DATATYPE_INT:
            case self::DATATYPE_LONG: return $this->castToInt($value);

            case self::DATATYPE_BOOLEAN: return $this->castToBoolean($value);

            case self::DATATYPE_STRING:
            case self::DATATYPE_CATEGORY: return $this->castToString($value);
        }

        return $value;
    }

    /**
     * @return array[]
     */
    public function toJson()
    {
        return [
            $this->name => $this->datatypes,
        ];
    }

    /**
     * @return mixed
     */
    protected function getMainDatatype()
    {
        return collect($this->datatypes)->first(function ($value) {
            return $value !== self::DATATYPE_NULL;
        });
    }

    /**
     * @param $value
     * @return int
     */
    protected function castToInt($value)
    {
        if ($value instanceof Carbon) {
            return (int) $value->timestamp;
        }

        return (int) $value;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function castToBoolean($value)
    {
        return (boolean) $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected function castToString($value)
    {
        return (string) $value;
    }

    /**
     * @param $value
     * @return float
     */
    protected function castToFloat($value)
    {
        return (float) $value;
    }
}
