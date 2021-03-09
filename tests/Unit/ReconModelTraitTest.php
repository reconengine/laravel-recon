<?php

namespace Recon\Tests\Unit;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Recon\Tests\BaseTest;
use Recon\Tests\Models\TestModelItem;
use Recon\Tests\Models\TestModelUser;

class ReconModelTraitTest extends BaseTest
{
    /** @test */
    public function isReconItem()
    {
        Http::fake();

        $testModel = TestModelItem::create([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);

        $this->assertTrue($testModel->isReconItem());
        $this->assertFalse($testModel->isReconUser());
    }

    /** @test */
    public function isReconUser()
    {
        Http::fake();

        $testModel = TestModelUser::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);

        $this->assertFalse($testModel->isReconItem());
        $this->assertTrue($testModel->isReconUser());
    }
}
