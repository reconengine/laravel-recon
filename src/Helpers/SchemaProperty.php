<?php


namespace Recon\Helpers;


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
     * @return array[]
     */
    public function toJson()
    {
        return [
            $this->name => $this->datatypes,
        ];
    }
}
