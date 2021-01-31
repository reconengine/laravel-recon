<?php


namespace LaravelMl;


use LaravelMl\Jobs\CreateRecordJob;
use LaravelMl\Jobs\DeleteRecordJob;
use LaravelMl\Jobs\UpdateRecordJob;

class LmlRecordObserver
{
    /**
     * @param LmlRecord $record
     */
    public function created($record)
    {
        if ($record->isTrainable()) {
            dispatch(new CreateRecordJob($record->ml()));
        }
    }

    /**
     * @param LmlRecord $record
     */
    public function updated($record)
    {
        if ($record->isTrainable()) {
            dispatch(new UpdateRecordJob($record->ml()));
        }
    }

    /**
     * @param LmlRecord $record
     */
    public function deleted($record)
    {
        if ($record->isTrainable()) {
            dispatch(new DeleteRecordJob($record->ml()));
        }
    }
}
