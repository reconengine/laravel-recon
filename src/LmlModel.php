<?php


namespace LaravelMl;


use LaravelMl\Api\ApiFacade;
use LaravelMl\Helpers\SchemaDefinition;
use LaravelMl\Observers\LmlUserObserver;

trait LmlModel
{
    /**
     * @return bool
     */
    public function isTrainable()
    {
        return true;
    }

    abstract protected function define(SchemaDefinition $definition);

    /**
     * @return array
     */
    public function toLmlJson()
    {
        return $this->getLmlDefinition()->only($this);
    }

    /**
     * @return SchemaDefinition
     */
    public function getLmlDefinition()
    {
        $definition = new SchemaDefinition();

        $this->define($definition);

        return $definition;
    }

    /**
     * @return bool
     */
    public function isLmlItem()
    {
        return in_array(LmlItem::class, class_uses(static::class));
    }

    /**
     * @return bool
     */
    public function isLmlUser()
    {
        return in_array(LmlUser::class, class_uses(static::class));
    }
}
