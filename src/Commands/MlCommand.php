<?php


namespace LaravelMl\Commands;


use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelMl\ApiFacade;
use LaravelMl\LaravelMl;
use LaravelMl\LaravelMlFacade;

class MlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize your model settings with Laravel.ai';

    protected $selectedModelNetworkResponse = null;

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

        if ($modelClasses->isEmpty()) {
            $this->error('No Laravel ML models detected. Did you set it up correctly?');
            return 1;
        }

        $modelClasses = collect($modelClasses)->mapWithKeys(function (string $modelClass) {
            $model = new $modelClass;
            return [$model->ml()->name() => $model];
        });

        $choice = $this->choice(
            'Which ML Model would you like to work with?',
            $modelClasses->keys()->toArray()
        );

        $modelClass = $modelClasses->get($choice);

        if (! $modelClass) {
            $this->error('Invalid choice. Please try again.');
            return 1;
        }

        $this->printModelInformation($modelClass);
        $action = $this->promptAction($choice);

        switch ($action) {
            case 'Create': return $this->store($modelClass);
            case 'Sync': return $this->sync($modelClass);
            case 'Delete': return $this->delete($modelClass);
            default:
                $this->error('Invalid choice. Please try again.');
                return 1;
        }
    }

    protected function printModelInformation($model)
    {
        /**
         * @var Response $remoteModelRecord
         */
        $remoteModelRecord = ApiFacade::showModel($model->ml()->name());

        if ($remoteModelRecord->status() === 404) {
            $this->warn('Model does not exist yet.');
            return;
        } elseif ($remoteModelRecord->status() !== 200) {
            $remoteModelRecord->throw();
        }

        $modelName = $remoteModelRecord['data']['name'];
        $modelType = $remoteModelRecord['data']['type'];

        $this->selectedModelNetworkResponse = $remoteModelRecord;
        $this->line("Model: {$modelName}");
        $this->line("Type:  {$modelType}");
    }

    protected function promptAction($choice)
    {
        $actions = $this->selectedModelNetworkResponse === null ? [
            'Create',
        ] : [
            'Sync',
            'Delete',
        ];

        return $this->choice(
            "Which action would you like to perform on {$choice}?",
            $actions
        );
    }

    protected function store($model)
    {
        $expectedModelType = $model->ml()->type();

        if (! $expectedModelType) {
            $this->error('No model type set in the config.');
            $this->info('Please set a model type in the config() method and try again.');
            return 1;
        }

        if (! $this->confirm("Detected type: {$expectedModelType}. Is this correct?")) {
            $this->info('Please update the model type in the config() method and try again.');
            return 1;
        }

        $response = ApiFacade::storeModel($model->ml()->name(), [
            'type' => $expectedModelType,
        ]);

        $response->throw(); // throw if not successful.

        return 0;
    }

    protected function sync($model)
    {
        ApiFacade::syncModel($model, function (Collection $models) {
            $firstId = $models->first()->id;
            $lastId = $models->last()->id;
            $this->info("Imported records {$firstId}-{$lastId}");
        });

        return 0;
    }

    protected function delete($model)
    {
        if ($this->confirm('Are you sure you want to delete this model? This cannot be undone.')) {
            $response = ApiFacade::deleteModel($model->ml()->name());

            $response->throw();
        }

        return 0;
    }
}
