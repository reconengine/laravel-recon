<?php


namespace Recon\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Recon\Api\ApiFacade;
use Recon\Exceptions\ReconCommandException;
use Recon\ReconItem;
use Recon\ReconUser;

class ReconSeedCommand extends BaseReconCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recon:seed {--database=} {--users}  {--items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        if ($this->option('users')) {
            $this->seedUsers();
        }

        if ($this->option('items')) {
            $this->seedItems();
        }

        return 0;
    }

    protected function seedUsers()
    {
        $userClass = $this->findSchemaClass(ReconUser::class);

        $this->line("Seeding with User class: {$userClass}");
        $this->line('');


        $userClass::orderBy('id')->chunk(250, function (Collection $users) {
            ApiFacade::putUsers($users);
            $firstId = $users->first()->id;
            $lastId = $users->last()->id;
            $this->info("Imported users {$firstId}-{$lastId}");
        });
    }

    protected function seedItems()
    {
        $itemClass = $this->findSchemaClass(ReconItem::class);

        $this->line("Seeding with Item class: {$itemClass}");
        $this->line('');

        $itemClass::orderBy('id')->chunk(250, function (Collection $items) {
            ApiFacade::putItems($items);
            $firstId = $items->first()->id;
            $lastId = $items->last()->id;
            $this->info("Imported items {$firstId}-{$lastId}");
        });
    }
}
