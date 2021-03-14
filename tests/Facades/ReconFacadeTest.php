<?php


namespace Recon\Tests\Facades;


use Recon\Models\ReconItem;
use Recon\Models\ReconUser;
use Recon\ReconFacade;
use Recon\Tests\BaseTest;
use Recon\Tests\Models\TestModelItem;
use Recon\Tests\Models\TestModelUser;

class ReconFacadeTest extends BaseTest
{
    /** @test */
    public function autoDetectModels()
    {
        $classes = ReconFacade::detectTrait(ReconItem::class);

        $this->assertEquals([
            TestModelItem::class,
        ], $classes->toArray());

        $classes = ReconFacade::detectTrait(ReconUser::class);

        $this->assertEquals([
            TestModelUser::class,
        ], $classes->toArray());
    }
}
