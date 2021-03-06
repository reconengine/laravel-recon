<?php

namespace LaravelMl\Tests\Unit;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LaravelMl\Tests\BaseTest;
use LaravelMl\Tests\Models\TestModelItem;
use LaravelMl\Tests\Models\TestModelUser;

class LmlModelTraitTest extends BaseTest
{
    /** @test */
    public function isLmlItem()
    {
        Http::fake();

        $testModel = TestModelItem::create([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);

        $this->assertTrue($testModel->isLmlItem());
        $this->assertFalse($testModel->isLmlUser());
    }

    /** @test */
    public function isLmlUser()
    {
        Http::fake();

        $testModel = TestModelUser::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);

        $this->assertFalse($testModel->isLmlItem());
        $this->assertTrue($testModel->isLmlUser());
    }
}
