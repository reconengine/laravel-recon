<?php

namespace LaravelMl\Tests\Observers;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LaravelMl\Tests\BaseTest;
use LaravelMl\Tests\Models\TestModelUser;

class UserObserverTest extends BaseTest
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
    public function userCreate()
    {
        Http::fake();

        $testModel = TestModelUser::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/users'])
                && $request->method() === 'POST'
                && $request['users'][0]['uid'] === $testModel->id
                && $request['users'][0]['metadata'] === [
                    'gender' => 'Male',
                    'age' => 22,
                    'salary' => 124567,
                ];
        });
    }

    /** @test */
    public function userUpdate()
    {
        $testModel = TestModelUser::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);

        Http::fake();
        $testModel->update([
            'salary' => 98765,
        ]);

        Http::assertSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/users'])
                && $request->method() === 'POST'
                && $request['users'][0]['uid'] === $testModel->id
                && $request['users'][0]['metadata'] === [
                    'gender' => 'Male',
                    'age' => 22,
                    'salary' => 98765,
                ];
        });
    }

    /** @test */
    public function userCreateRespectsIsTrainable()
    {
        Http::fake();

        $testModel = new TestModelUser([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);
        $testModel->isTrainable = false;
        $testModel->save();

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/users'])
                && $request->method() === 'POST'
                && $request['users'][0]['uid'] === $testModel->id;
        });
    }

    /** @test */
    public function userUpdateRespectsIsTrainable()
    {
        $testModel = TestModelUser::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);

        Http::fake();
        $testModel->isTrainable = false;
        $testModel->update([
            'salary' => 98765,
        ]);

        Http::assertNotSent(function (Request $request) use ($testModel) {
            return Str::contains($request->url(), ['/api/databases/::database::/users'])
                && $request->method() === 'POST'
                && $request['users'][0]['uid'] === $testModel->id;
        });
    }
}
