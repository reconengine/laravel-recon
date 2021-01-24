<?php


namespace LaravelMl;


use LaravelMl\Jobs\CreateModelItemJob;
use LaravelMl\Jobs\DeleteModelItemJob;
use LaravelMl\Jobs\UpdateModelItemJob;

class MlModelObserver
{
    /**
     * @param MlModel $modelItem
     */
    public function created($modelItem)
    {
        if ($modelItem->isTrainable()) {
            dispatch(new CreateModelItemJob($modelItem));
        }
    }

    /**
     * @param MlModel $modelItem
     */
    public function updated($modelItem)
    {
        if ($modelItem->isTrainable()) {
            dispatch(new UpdateModelItemJob($modelItem));
        }
    }

    /**
     * @param MlModel $modelItem
     */
    public function deleted($modelItem)
    {
        if ($modelItem->isTrainable()) {
            dispatch(new DeleteModelItemJob($modelItem));
        }
    }
}
