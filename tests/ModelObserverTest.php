<?php

namespace LaravelMl\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/items'])
                && $request->method() === 'POST'
                && $request['features'][0] === 25
                && $request['label'] === 50000
                && $request['identifier'] === strval($testModel->id);
        });
    }

    /** @test */
    public function modelUpdate()
    {
        Http::fake();

        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        $testModel->update([
            'salary' => 60000,
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/items/' . $testModel->ml()->id()])
                && $request->method() === 'PUT'
                && $request['features'][0] === 25
                && $request['label'] === 60000
                && $request['identifier'] === strval($testModel->id);
        });
    }

    /** @test */
    public function modelDelete()
    {
        Http::fake();

        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        $testModel->delete();

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/items/' . $testModel->ml()->id()])
                && $request->method() === 'DELETE';
        });
    }

    /** @test */
    public function modelCreateRespectsIsTrainable()
    {
        Http::fake();

        $testModel = new TestModel([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);
        $testModel->isTrainable = false;
        $testModel->save();

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/items'])
                && $request->method() === 'POST'
                && $request['features'][0] === 25
                && $request['label'] === 50000
                && $request['identifier'] === strval($testModel->id);
        });
    }

    /** @test */
    public function modelUpdateRespectsIsTrainable()
    {
        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        Http::fake();
        $testModel->isTrainable = false;
        $testModel->update([
            'salary' => 60000,
        ]);

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/items/' . $testModel->ml()->id()])
                && $request->method() === 'PUT'
                && $request['features'][0] === 25
                && $request['label'] === 60000
                && $request['identifier'] === strval($testModel->id);
        });
    }

    /** @test */
    public function modelDeleteRespectsIsTrainable()
    {
        $testModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);

        Http::fake();
        $testModel->isTrainable = false;
        $testModel->delete();

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/models/' . $testModel->ml()->name() . '/items/' . $testModel->ml()->id()])
                && $request->method() === 'DELETE';
        });
    }
}
