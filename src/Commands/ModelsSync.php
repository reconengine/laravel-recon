<?php


namespace LaravelMl\Commands;


use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use LaravelMl\LaravelMl;
use LaravelMl\LaravelMlFacade;

class ModelsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize your model settings with Laravel.ai';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelClasses = LaravelMlFacade::detectMlModels();

        foreach ($modelClasses as $modelClass)
        {
//
        }

        return 0;
    }
}
