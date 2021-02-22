<?php


namespace LaravelMl\Observers;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\Jobs\CreateItemJob;
use LaravelMl\Jobs\UpdateItemJob;

class LmlItemObserver
{
    /**
     * @param Model $record
     */
    public function created($record)
    {
        if ($record->isTrainable()) {
            dispatch(new CreateItemJob($record));
        }
    }

    /**
     * @param Model $record
     */
    public function updated($record)
    {
        if ($record->isTrainable()) {
            dispatch(new UpdateItemJob($record));
        }
    }
}
