<?php


namespace Recon;


use Recon\Api\ApiFacade;
use Recon\Helpers\SchemaDefinition;
use Recon\Observers\ReconUserObserver;

trait ReconModel
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
    public function toReconJson()
    {
        return $this->getReconDefinition()->only($this);
    }

    /**
     * @return SchemaDefinition
     */
    public function getReconDefinition()
    {
        $definition = new SchemaDefinition();

        $this->define($definition);

        return $definition;
    }

    /**
     * @return bool
     */
    public function isReconItem()
    {
        return in_array(ReconItem::class, class_uses(static::class));
    }

    /**
     * @return bool
     */
    public function isReconUser()
    {
        return in_array(ReconUser::class, class_uses(static::class));
    }
}
