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

    public $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($modelItem)
    {
        $this->config = $modelItem->ml();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ApiFacade::deleteModelItem($this->config);
    }
}
