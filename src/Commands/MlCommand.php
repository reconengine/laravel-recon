<?php


namespace LaravelMl\Commands;


use LaravelMl\Api\ApiFacade;
use LaravelMl\Exceptions\LmlCommandException;
use LaravelMl\Exceptions\LmlConfigValidationException;
use LaravelMl\LmlItem;
use LaravelMl\LmlUser;

class MlCommand extends BaseMlCommand
{
    const POSSIBLE_DATABASE_COMMANDS = [
        'Seed' => 'Seed',
        'Retrain' => 'Retrain',
        'Delete' => 'Delete',
        'Nevermind' => 'Nevermind, just lookin\'',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize your database and record settings with laravelml.com';

    /**
     * @var string
     */
    protected $currentDatabase;

    /**
     * @var string[]
     */
    protected $allDatabases;

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
        try {
            $this->validateApiKey();
            $this->currentDatabase = $this->getCurrentDatabase();
            $this->allDatabases = $this->getAllDatabases();
            $this->promptDatabaseOptions();

            $action = $this->promptAction();
            $this->{'handle'.$action}();
        } catch (LmlCommandException $exception) {
            $this->error($exception->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * @throws LmlCommandException
     */
    protected function validateApiKey()
    {
        if (! config('laravel-ml.token')) {
            throw new LmlCommandException('Missing API Key. Add ML_API_TOKEN={apiKey} to your .env file. You can get a key at https://laravelml.com');
        }
    }

    /**
     * @return string[]
     */
    protected function getAllDatabases()
    {
        $databasesNetworkResponse = ApiFacade::getDatabases();

        $databasesNetworkResponse->throw();

        return collect($databasesNetworkResponse->json()['data'])->pluck('name')->toArray();
    }

    /**
     * We have now loaded allDatabases and the currentDatabase (from config).
     *
     * 1. If no local database, then
     *      a. if remote, prompt to use a remote one and write to .env file
     *      b. if no remote, prompt to create a database with name input
     * 2. If local database, then
     *      a. if no remote, promt to create a new one with that name
     *      b. if remote, display all remote with (default) after
     */
    protected function promptDatabaseOptions()
    {
        if ($this->currentDatabase) {
            if ($this->allDatabases) {
                $this->displayAllDatabases();
            } else {
                $this->promptCreateDatabaseWithName($this->currentDatabase);
            }
        } else {
            if ($this->allDatabases) {
                $this->promptSelectARemoteAndWriteToEnvFile();
            } else {
                $this->promptCreateDatabaseWithUserSuppliedName();
            }
        }
    }

    /**
     *
     */
    protected function promptSelectARemoteAndWriteToEnvFile()
    {
        if ($selectedRemoteDatabase = $this->choice(
            "No local database set. Would you like to use an existing database?",
            $this->allDatabases
        )) {
            $this->writeToEnvFile("ML_DEFAULT_DATABASE={$selectedRemoteDatabase}");
            $this->currentDatabase = $selectedRemoteDatabase;
        }
    }

    /**
     *
     */
    protected function promptCreateDatabaseWithUserSuppliedName()
    {
        if ($userInput = $this->ask("Not local database set. Let's make you a new one. What would you like to call it (alphanumeric, '-', or '_')?")) {
            $this->promptCreateDatabaseWithName($userInput);
            $this->writeToEnvFile("ML_DEFAULT_DATABASE={$userInput}");
            $this->currentDatabase = $userInput;
        }
    }

    /**
     * @param string $name
     */
    protected function promptCreateDatabaseWithName(string $name)
    {
        if ($this->confirm("Would you like to create database: '{$name}'?")) {
            $this->createDatabase($name);
        }
    }

    /**
     *
     */
    protected function displayAllDatabases()
    {
        if (! $this->doesDefaultDatabaseExistRemotely()) {
            $this->warn("Database '{$this->currentDatabase}' does not exist on laravel-ml.com.");
            $this->promptCreateDatabaseWithName($this->currentDatabase);
        }

        $this->line('');
        $this->line('**************************************************************');
        $this->line('** Databases:');
        foreach ($this->allDatabases as $i => $database) {
            $index = $i + 1;
            $suxif = $database === $this->currentDatabase ? ' (default)' : '';
            $this->line("**  {$index}. {$database}{$suxif}");
        }
        $this->line('**************************************************************');
        $this->line('');
    }

    /**
     * @param string $name
     * @throws LmlConfigValidationException
     */
    protected function createDatabase(string $name)
    {
        $userSchemaDefinition = $this->findSchemaDefinition(LmlUser::class);
        $itemSchemaDefinition = $this->findSchemaDefinition(LmlItem::class);

        $response = ApiFacade::storeDatabase($name, $userSchemaDefinition, $itemSchemaDefinition);
        $response->throw();
    }

    /**
     * Write $string to a new line in the .env file.
     *
     * @param string $string
     */
    protected function writeToEnvFile(string $string)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, file_get_contents($path) . "{$string}");
        }
    }

    /**
     * @return bool
     */
    protected function doesDefaultDatabaseExistRemotely()
    {
        return in_array($this->currentDatabase, $this->allDatabases);
    }

    /**
     * @param $choice
     * @return array|string
     * @throws LmlCommandException
     */
    protected function promptAction()
    {
        $options = collect(self::POSSIBLE_DATABASE_COMMANDS);

        $choice = $this->choice(
            "Which action would you like to perform on '{$this->currentDatabase}'?",
            $options->values()->toArray()
        );

        // lookup the command name from the user friendly label.
        $command = $options->search($choice);
        if (! $command) {
            throw new LmlCommandException('Unsupported command.');
        }

        if (! method_exists($this, 'handle'.$command)) {
            throw new LmlCommandException('Unsupported command.');
        }

        return $command;
    }

    /**
     *
     */
    protected function handleSeed()
    {
        $this->call('ml:seed', [
            '--database' => $this->currentDatabase,
        ]);
    }

    /**
     *
     */
    protected function handleRetrain()
    {
        $this->call('ml:retrain', [
            '--database' => $this->currentDatabase,
        ]);
    }

    /**
     *
     */
    protected function handleDelete()
    {
        $this->warn('Deleting a database from the command line is currently not supported. Please delete on laravel-ml.com.');
    }

    /**
     *
     */
    protected function handleNevermind()
    {
        $this->line('I appreciate you 👋');
    }

    protected function checkForNetworkError()
    {

    }
}
