<?php

namespace LaravelMl\Tests\Observers;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LaravelMl\Tests\BaseTest;
use LaravelMl\Tests\Models\TestModelItem;

class ItemObserverTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        config([
            'laravel-ml' => [
                'database' => '::database::',
            ]
        ]);
    }

    /** @test */
    public function itemCreate()
    {
        Http::fake();

        $testModel = TestModelItem::create([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/items'])
                && $request->method() === 'POST'
                && $request['items'][0]['iid'] === $testModel->id
                && $request['items'][0]['metadata'] === [
                    'color' => 'green',
                    'rating' => 4.45,
                    'ratings' => 132,
                ];
        });
    }

    /** @test */
    public function itemUpdate()
    {
        $testModel = TestModelItem::create([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);

        Http::fake();
        $testModel->update([
            'color' => 'red',
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/items'])
                && $request->method() === 'POST'
                && $request['items'][0]['iid'] === $testModel->id
                && $request['items'][0]['metadata'] === [
                    'color' => 'red',
                    'rating' => 4.45,
                    'ratings' => 132,
                ];
        });
    }

    /** @test */
    public function itemCreateRespectsIsTrainable()
    {
        Http::fake();

        $testModel = new TestModelItem([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);
        $testModel->isTrainable = false;
        $testModel->save();

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/items'])
                && $request->method() === 'POST'
                && $request['items'][0]['iid'] === $testModel->id;
        });
    }

    /** @test */
    public function itemUpdateRespectsIsTrainable()
    {
        $testModel = TestModelItem::create([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);

        Http::fake();
        $testModel->isTrainable = false;
        $testModel->update([
            'color' => 'red',
        ]);

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/items'])
                && $request->method() === 'POST'
                && $request['items'][0]['iid'] === $testModel->id;
        });
    }
}
