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
use LaravelMl\MlModelConfig;

class UpdateModelItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MlModelConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ApiFacade::updateModelItem($this->config);
    }
}
