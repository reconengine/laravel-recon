<?php


namespace Recon\Observers;


use Illuminate\Database\Eloquent\Model;
use Recon\Jobs\CreateItemJob;
use Recon\Jobs\UpdateItemJob;

class ReconItemObserver
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
