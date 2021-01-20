<?php

namespace LaravelMl\Tests;

use Orchestra\Testbench\TestCase;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LaravelMlServiceProvider;
use LaravelMl\Tests\Models\TestModel;

class ModelDetectionTest extends BaseTest
{
    /** @test */
    public function autoDetectModels()
    {
        $classes = LaravelMlFacade::detectMlModels();

        $this->assertEquals([
            TestModel::class,
        ], $classes->toArray());
    }
}
