<?php


namespace Recon\Observers;


use Illuminate\Database\Eloquent\Model;

class ReconUserObserver
{
    /**
     * @param Model $record
     */
    public function saved($record)
    {
        $record->trainable();
    }
}
