<?php

namespace LaravelMl\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelMl\ApiFacade;
use LaravelMl\LaravelMlFacade;

class DeleteModelItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $modelItemIdentifier;
    public $modelName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($modelItem)
    {
        $this->modelItemIdentifier = $modelItem->ml()->id();
        $this->modelName = $modelItem->ml()->name();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ApiFacade::deleteModelItem($this->modelName, $this->modelItemIdentifier);
    }
}
