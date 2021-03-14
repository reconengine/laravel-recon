<?php


namespace Recon\Commands;


use Illuminate\Contracts\Events\Dispatcher;
use Recon\Events\ModelsImported;
use Recon\Models\ReconItem;
use Recon\Models\ReconUser;

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
     * @param Dispatcher $events
     * @return int
     */
    public function handle(Dispatcher $events)
    {
        if ($this->option('users')) {
            $this->seedUsers($events);
        }

        if ($this->option('items')) {
            $this->seedItems($events);
        }

        return 0;
    }

    protected function seedUsers(Dispatcher $events)
    {
        $userClass = $this->findSchemaClass(ReconUser::class);

        $this->line("Seeding with User class: {$userClass}");
        $this->line('');

        $events->listen(ModelsImported::class, function ($event) use ($userClass) {
            $key = $event->models->last()->id;

            $this->line('<comment>Imported ['.$userClass.'] models up to ID:</comment> '.$key);
        });

        $userClass::makeAllTrainable();

        $events->forget(ModelsImported::class);
    }

    protected function seedItems(Dispatcher $events)
    {
        $itemClass = $this->findSchemaClass(ReconItem::class);

        $this->line("Seeding with Item class: {$itemClass}");
        $this->line('');

        $events->listen(ModelsImported::class, function ($event) use ($itemClass) {
            $key = $event->models->last()->id;

            $this->line('<comment>Imported ['.$itemClass.'] models up to ID:</comment> '.$key);
        });

        $itemClass::makeAllTrainable();

        $events->forget(ModelsImported::class);
    }
}
