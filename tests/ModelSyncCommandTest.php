<?php

namespace LaravelMl\Tests;

use Orchestra\Testbench\TestCase;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LaravelMlServiceProvider;
use LaravelMl\Tests\Models\TestModel;

class ModelSyncCommandTest extends BaseTest
{
    /** @test */
    public function modelSyncCommandWillAutoDetectModels()
    {
        $classes = LaravelMlFacade::detectMlModels();

        $this->assertEquals([
            TestModel::class,
        ], $classes->toArray());
    }
}
