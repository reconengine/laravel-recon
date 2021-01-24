<?php

namespace LaravelMl\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LaravelMlServiceProvider;
use LaravelMl\Tests\Models\TestModel;

class ModelPredictTest extends BaseTest
{
    /** @test */
    public function modelPrediction()
    {
        Http::fake([
            '*' => Http::response([
                'duration' => 0.009881019592285156,
                'data' => [
                    3379.7992230292684
                ],
            ], 200),
        ]);

        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        $response = $testModel->predict();

        $this->assertEquals(3379.7992230292684, $response);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/predict'])
                && $request->method() === 'POST'
                && $request['samples'][0][0] === 25;
        });
    }
}
