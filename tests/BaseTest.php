<?php

namespace LaravelMl\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LaravelMlServiceProvider;
use LaravelMl\Tests\Models\TestModel;

class BaseTest extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->loadMigrationsFrom(__DIR__ . '/Models/migrations');

        LaravelMlFacade::partialMock()->shouldReceive('detectDatabasePath')->andReturn(__DIR__ . '/Models');
        LaravelMlFacade::partialMock()->shouldReceive('detectDatabaseNamespace')->andReturn('LaravelMl\\Tests\\Models');
    }

    protected function getPackageProviders($app)
    {
        return [LaravelMlServiceProvider::class];
    }
}
