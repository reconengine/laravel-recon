<?php


namespace LaravelMl\Observers;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\Jobs\CreateUserJob;
use LaravelMl\Jobs\UpdateUserJob;

class LmlUserObserver
{
    /**
     * @param Model $record
     */
    public function created($record)
    {
        if ($record->isTrainable()) {
            dispatch(new CreateUserJob($record));
        }
    }

    /**
     * @param Model $record
     */
    public function updated($record)
    {
        if ($record->isTrainable()) {
            dispatch(new UpdateUserJob($record));
        }
    }
}
