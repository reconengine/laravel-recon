<?php


namespace Recon\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Recon\Api\ApiFacade;
use Recon\Exceptions\ReconCommandException;
use Recon\Exceptions\ReconConfigValidationException;
use Recon\Helpers\SchemaDefinition;
use Recon\ReconFacade;
use Recon\ReconItem;
use Recon\ReconUser;

class BaseReconCommand extends Command
{

    /**
     * @return string|null
     */
    protected function getCurrentDatabase()
    {
        return $this->option('database') ?? config('recon.database');
    }

    /**
     * @param string $trait
     * @return SchemaDefinition
     * @throws ReconConfigValidationException
     */
    protected function findSchemaDefinition(string $trait): SchemaDefinition
    {
        $schemaClass = $this->findSchemaClass($trait);

        return (new $schemaClass)->getReconDefinition();
    }

    /**
     * @param string $trait
     * @return string
     * @throws ReconConfigValidationException
     */
    protected function findSchemaClass(string $trait): string
    {
        $classes = ReconFacade::detectTrait($trait);

        if ($classes->count() !== 1) {
            throw new ReconConfigValidationException("Must be exactly 1 model using trait: {$trait}. Found: {$classes->count()}");
        }

        return $classes->first();
    }
}
