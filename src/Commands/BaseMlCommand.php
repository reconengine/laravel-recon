<?php


namespace LaravelMl\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use LaravelMl\Api\ApiFacade;
use LaravelMl\Exceptions\LmlCommandException;
use LaravelMl\Exceptions\LmlConfigValidationException;
use LaravelMl\Helpers\SchemaDefinition;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LmlItem;
use LaravelMl\LmlUser;

class BaseMlCommand extends Command
{

    /**
     * @return string|null
     */
    protected function getCurrentDatabase()
    {
        return $this->option('database') ?? config('laravel-ml.database');
    }

    /**
     * @param string $trait
     * @return SchemaDefinition
     * @throws LmlConfigValidationException
     */
    protected function findSchemaDefinition(string $trait): SchemaDefinition
    {
        $schemaClass = $this->findSchemaClass($trait);

        return (new $schemaClass)->getLmlDefinition();
    }

    /**
     * @param string $trait
     * @return string
     * @throws LmlConfigValidationException
     */
    protected function findSchemaClass(string $trait): string
    {
        $classes = LaravelMlFacade::detectTrait($trait);

        if ($classes->count() !== 1) {
            throw new LmlConfigValidationException("Must be exactly 1 model using trait: {$trait}. Found: {$classes->count()}");
        }

        return $classes->first();
    }
}
