<?php


namespace Recon\Observers;


use Illuminate\Database\Eloquent\Model;
use Recon\Jobs\CreateUserJob;
use Recon\Jobs\UpdateUserJob;

class ReconUserObserver
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
