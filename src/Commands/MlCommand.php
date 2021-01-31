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
    protected $description = 'Syncronize your database and record settings with laravelml.com';

    protected $selectedDatabaseNetworkResponse = null;

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
        $existingApiKey = config('laravel-ml.token');

        if (! $existingApiKey) {
            $this->error('Missing API Key. Add ML_API_TOKEN={apiKey} to your .env file. You can get a key at https://laravelml.com');
            return 1;
        }

        $modelClasses = LaravelMlFacade::detectDatabases();

        if ($modelClasses->isEmpty()) {
            $this->error('No Laravel ML databases detected. Did you set it up correctly?');
            return 1;
        }

        $modelClasses = collect($modelClasses)->mapWithKeys(function (string $modelClass) {
            $model = new $modelClass;
            return [$model->ml()->database()->name() => $model];
        });

        $choice = $this->choice(
            'Which ML Record Definition would you like to work with?',
            $modelClasses->keys()->toArray()
        );

        $modelClass = $modelClasses->get($choice);

        if (! $modelClass) {
            $this->error('Invalid choice. Please try again.');
            return 1;
        }

        $this->printModelInformation($modelClass);
        $modelClass->ml()->database()->validate();
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
        $remoteModelRecord = ApiFacade::showDatabase($model->ml()->database());

        if ($remoteModelRecord->status() === 404) {
            $this->warn('Database does not exist yet.');
            return;
        } elseif ($remoteModelRecord->status() !== 200) {
            $remoteModelRecord->throw();
        }

        $modelName = $remoteModelRecord['data']['name'];
        $modelType = $remoteModelRecord['data']['type'];

        $this->selectedDatabaseNetworkResponse = $remoteModelRecord;
        $this->line("Database:  {$modelName}");
        $this->line("Type:      {$modelType}");
    }

    protected function promptAction($choice)
    {
        $actions = $this->selectedDatabaseNetworkResponse === null ? [
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
        $expectedModelType = $model->ml()->database()->type();
        $expectedDatatype = $model->ml()->database()->datatype();

        if (! $expectedModelType) {
            $this->error('No model type set in the config.');
            $this->info('Please set a model type in the config() method and try again.');
            return 1;
        }

        if (! $expectedDatatype) {
            $this->error('No datatype set in the config.');
            $this->info('Please set a datatype in the config() method and try again.');
            return 1;
        }

        if (! $this->confirm("Detected type: {$expectedModelType} ({$expectedDatatype}). Is this correct?")) {
            $this->info('Please update the model type in the config() method and try again.');
            return 1;
        }

        $response = ApiFacade::storeDatabase($model->ml()->database());

        $response->throw(); // throw if not successful.

        return 0;
    }

    protected function sync($model)
    {
        ApiFacade::syncDatabase($model, function (Collection $models) {
            $firstId = $models->first()->id;
            $lastId = $models->last()->id;
            $this->info("Imported records {$firstId}-{$lastId}");
        });

        return 0;
    }

    protected function delete($model)
    {
        if ($this->confirm('Are you sure you want to delete this database? This cannot be undone.')) {
            $response = ApiFacade::deleteDatabase($model->ml()->database());

            $response->throw();
        }

        return 0;
    }
}
