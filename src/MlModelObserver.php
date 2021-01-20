<?php


namespace LaravelMl;


use LaravelMl\Jobs\CreateModelItemJob;
use LaravelMl\Jobs\DeleteModelItemJob;
use LaravelMl\Jobs\UpdateModelItemJob;

class MlModelObserver
{
    public function created($modelItem)
    {
        dispatch(new CreateModelItemJob($modelItem));
    }

    public function updated($modelItem)
    {
        dispatch(new UpdateModelItemJob($modelItem));
    }

    public function deleted($modelItem)
    {
        dispatch(new DeleteModelItemJob($modelItem));
    }
}
