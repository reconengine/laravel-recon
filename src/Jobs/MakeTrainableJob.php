<?php

namespace Recon\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Recon\Api\ApiFacade;

class MakeTrainableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $items;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->items->isEmpty()) {
            return;
        }

        $first = $this->items->first();

        if ($first->isReconItem()) {
            ApiFacade::putItems($this->items);
        } elseif ($first->isReconUser()) {
            ApiFacade::putUsers($this->items);
        }
    }
}
