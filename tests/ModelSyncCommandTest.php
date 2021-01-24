<?php

namespace LaravelMl\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LaravelMl\Api;
use LaravelMl\LaravelMlFacade;
use LaravelMl\Tests\Models\TestModel;

class ModelSyncCommandTest extends BaseTest
{
    /** @test */
    public function modelFailsCalmlyWhenNoDetectedModelsExist()
    {
        LaravelMlFacade::shouldReceive('detectMlModels')->andReturn(collect());

        $this->artisan('ml')
            ->expectsOutput('No Laravel ML models detected. Did you set it up correctly?')
            ->assertExitCode(1);
    }

    /** @test */
    public function modelMlCommandHandlesInvalidModel()
    {
        $this->artisan('ml')
            ->expectsQuestion('Which ML Model would you like to work with?', 'test')
            ->expectsOutput('Invalid choice. Please try again.')
            ->assertExitCode(1);
    }

    /** @test */
    public function modelMlCommandHandlesInvalidCommand()
    {
        $modelName = (new TestModel())->ml()->name();

        Http::fake([
            '*' => Http::response([], 404),
        ]);

        $this->artisan('ml')
            ->expectsQuestion('Which ML Model would you like to work with?', $modelName)
            ->expectsOutput("Model does not exist yet.")
            ->expectsQuestion("Which action would you like to perform on {$modelName}?", 'test')
            ->expectsOutput('Invalid choice. Please try again.')
            ->assertExitCode(1);
    }

    /** @test */
    public function modelCreateModel()
    {
        $modelName = (new TestModel())->ml()->name();
        $type = (new TestModel())->ml()->type();

        Http::fake([
            Api::HOST . '/models/' . $modelName => Http::response([], 404),
            Api::HOST . '/models' => Http::response([
                'data' => [
                    'type' => $type,
                    'name' => $modelName,
                ]
            ], 200),
        ]);

        $this->artisan('ml')
            ->expectsQuestion('Which ML Model would you like to work with?', $modelName)
            ->expectsOutput("Model does not exist yet.")
            ->expectsQuestion("Which action would you like to perform on {$modelName}?", 'Create')
            ->expectsQuestion("Detected type: {$type}. Is this correct?", 'yes')
            ->assertExitCode(0);

        Http::assertSent(function (Request $request) use ($modelName, $type) {
            return Str::contains($request->url(), ['/api/models'])
                && $request->method() === 'POST'
                && $request['name'] === $modelName
                && $request['type'] === $type;
        });
    }

    /** @test */
    public function modelSyncModel()
    {
        $modelName = (new TestModel())->ml()->name();
        $type = (new TestModel())->ml()->type();

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'name' => $modelName,
                    'type' => $type,
                ],
            ], 200),
        ]);

        $existingModel = TestModel::create([
            'name' => 'zach',
            'age' => 25,
            'salary' => 50000,
        ]);
        $this->artisan('ml')
            ->expectsQuestion('Which ML Model would you like to work with?', $modelName)
            ->expectsQuestion("Which action would you like to perform on {$modelName}?", 'Sync')
            ->assertExitCode(0);

        Http::assertSent(function (Request $request) use ($modelName, $existingModel) {
            return Str::contains($request->url(), ["/api/models/{$modelName}/train"])
                && $request->method() === 'POST'
                && sizeof($request['samples']) === 1
                && $request['samples'][0]['features'][0] === 25
                && $request['samples'][0]['label'] === 50000
                && $request['samples'][0]['identifier'] === strval($existingModel->id);
        });
    }

    /** @test */
    public function modelDeleteModel()
    {
        $modelName = (new TestModel())->ml()->name();

        Http::fake([
            Api::HOST . '/models/' . $modelName => Http::sequence()
                ->pushStatus(404) // the SHOW
                ->pushStatus(200), // the DELETE
        ]);

        $this->artisan('ml')
            ->expectsQuestion('Which ML Model would you like to work with?', $modelName)
            ->expectsOutput("Model does not exist yet.")
            ->expectsQuestion("Which action would you like to perform on {$modelName}?", 'Delete')
            ->expectsQuestion("Are you sure you want to delete this model? This cannot be undone.", 'yes')
            ->assertExitCode(0);

        Http::assertSent(function (Request $request) use ($modelName) {
            return Str::contains($request->url(), ['/api/models/' . $modelName])
                && $request->method() === 'DELETE';
        });
    }

}
