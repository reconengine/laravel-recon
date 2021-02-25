<?php


namespace LaravelMl\Helpers;


use Illuminate\Database\Eloquent\Model;

class SchemaDefinition
{
    protected $properties;

    public function __construct()
    {
        $this->properties = collect();
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function int($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_INT);
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function double($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_DOUBLE);
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function float($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_FLOAT);
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function long($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_LONG);
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function boolean($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_BOOLEAN);
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function string($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_STRING);
    }

    /**
     * @param $name
     * @return SchemaProperty
     */
    public function category($name)
    {
        return $this->addProperty($name, SchemaProperty::DATATYPE_CATEGORY);
    }

    /**
     * @return mixed
     */
    public function toJson()
    {
        return $this->properties->reduce(function (array $json, SchemaProperty $property) {
            return $json + $property->toJson();
        }, []);
    }

    /**
     * @param Model $model
     * @return array
     */
    public function only(Model $model)
    {
        $keys = $this->properties->pluck('name');

        return $model->only($keys->toArray());
    }

    /**
     * @param $name
     * @param $datatype
     * @return SchemaProperty
     * @throws \LaravelMl\Exceptions\LmlConfigValidationException
     */
    protected function addProperty($name, $datatype)
    {
        $property = new SchemaProperty($name, $datatype);

        $this->properties->push($property);

        return $property;
    }
}
