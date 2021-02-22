<?php

namespace LaravelMl\Tests;

use Orchestra\Testbench\TestCase;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LaravelMlServiceProvider;
use LaravelMl\Tests\Models\TestModelItem;

class DatabaseDetectionTest extends BaseTest
{
    /** @test */
    public function autoDetectModels()
    {
        $classes = LaravelMlFacade::detectDatabases();

        $this->assertEquals([
            TestModelItem::class,
        ], $classes->toArray());
    }
}
