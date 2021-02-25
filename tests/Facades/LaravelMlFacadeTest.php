<?php


namespace LaravelMl\Tests\Facades;


use LaravelMl\LaravelMlFacade;
use LaravelMl\LmlItem;
use LaravelMl\LmlUser;
use LaravelMl\Tests\BaseTest;
use LaravelMl\Tests\Models\TestModelItem;
use LaravelMl\Tests\Models\TestModelUser;

class LaravelMlFacadeTest extends BaseTest
{
    /** @test */
    public function autoDetectModels()
    {
        $classes = LaravelMlFacade::detectTrait(LmlItem::class);

        $this->assertEquals([
            TestModelItem::class,
        ], $classes->toArray());

        $classes = LaravelMlFacade::detectTrait(LmlUser::class);

        $this->assertEquals([
            TestModelUser::class,
        ], $classes->toArray());
    }
}
