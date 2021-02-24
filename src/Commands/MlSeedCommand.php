<?php


namespace LaravelMl\Commands;


use Illuminate\Console\Command;
use LaravelMl\Api\ApiFacade;
use LaravelMl\Exceptions\LmlCommandException;

class MlSeedCommand extends Command
{
    use CommandHasDatabaseInput;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml:seed {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize your database and record settings with laravelml.com';

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
        $database = $this->getCurrentDatabase();

        ApiFacade::seedDatabase($database);

        return 0;
    }
}
