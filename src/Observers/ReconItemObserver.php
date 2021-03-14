<?php


namespace Recon\Observers;


use Illuminate\Database\Eloquent\Model;

class ReconItemObserver
{
    /**
     * @param Model $record
     */
    public function saved($record)
    {
        $record->trainable();
    }
}
