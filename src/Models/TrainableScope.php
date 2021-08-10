<?php


namespace Recon\Models;


use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Recon\Events\ModelsImported;

class TrainableScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        //
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(EloquentBuilder $builder)
    {
        $builder->macro('trainable', function (EloquentBuilder $builder) {
            $builder->chunkById(250, function ($models) {
                $models->filter->isTrainable()->trainable();

                event(new ModelsImported($models));
            });
        });
    }
}
