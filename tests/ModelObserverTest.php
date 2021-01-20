<?php

namespace LaravelMl\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use LaravelMl\Api;
use LaravelMl\LaravelMl;
use LaravelMl\LaravelMlFacade;
use LaravelMl\LaravelMlServiceProvider;
use LaravelMl\Tests\Models\TestModel;

class ModelObserverTest extends BaseTest
{
    /** @test */
    public function modelCreate()
    {
        Http::fake();

        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/models/' . $testModel->getMlName() . '/items'])
                && $request->method() === 'POST'
                && $request['features'][0] === 'zach'
                && $request['features'][1] === 25
                && $request['label'] === 50000
                && $request['identifier'] === $testModel->id;
        });
    }

    /** @test */
    public function modelUpdate()
    {
        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        Http::fake();

        $testModel->update([
            'salary' => 60000,
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/models/' . $testModel->getMlName() . '/items/' . $testModel->getMlId()])
                && $request->method() === 'PUT'
                && $request['features'][0] === 'zach'
                && $request['features'][1] === 25
                && $request['label'] === 60000
                && $request['identifier'] === $testModel->id;
        });
    }

    /** @test */
    public function modelDelete()
    {
        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        Http::fake();

        $testModel->delete();

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/models/' . $testModel->getMlName() . '/items/' . $testModel->getMlId()])
                && $request->method() === 'DELETE';
        });
    }
}
