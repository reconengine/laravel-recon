<?php


namespace Recon\Models;


use Illuminate\Routing\Pipeline;
use Illuminate\Support\Collection as BaseCollection;
use Recon\Helpers\SchemaDefinition;
use Recon\Jobs\MakeTrainableJob;

trait ReconModel
{
    /**
     * Hook
     */
    public static function bootReconModel()
    {
        static::addGlobalScope(new TrainableScope);

        (new static)->registerTrainableMacros();
    }

    /**
     * Register the searchable macros.
     *
     * @return void
     */
    public function registerTrainableMacros()
    {
        $self = $this;

        BaseCollection::macro('trainable', function () use ($self) {
            $self->queueMakeTrainable($this);
        });
    }

    /**
     * Dispatch the job to make the given models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function queueMakeTrainable($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        if (config('recon.queue')) {
            dispatch((new MakeTrainableJob($models)));
        } else {
            dispatch_now((new MakeTrainableJob($models)));
        }

    }


    /**
     * Make all instances of the model searchable.
     *
     * @param  int  $chunk
     * @return void
     */
    public static function makeAllTrainable()
    {
        $self = new static;

        $self->newQuery()
            ->orderBy($self->getKeyName())
            ->trainable();
    }

    /**
     * Make the given model instance searchable.
     *
     * @return void
     */
    public function trainable()
    {
        if ($this->isTrainable()) {
            $this->newCollection([$this])->trainable();
        }
    }

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
