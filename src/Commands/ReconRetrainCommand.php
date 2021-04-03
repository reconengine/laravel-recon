<?php


namespace Recon\Commands;


use Recon\Api\ApiFacade;

class ReconRetrainCommand extends BaseReconCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recon:retrain {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrain your database.';

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

        ApiFacade::retrainDatabase($database);

        return 0;
    }
}
