<?php

namespace Recon\Tests\Commands;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Recon\Api\Api;
use Recon\Tests\BaseTest;
use Recon\Tests\Models\TestModelItem;
use Recon\Tests\Models\TestModelUser;

class ReconCommandTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        config([
            'recon' => [
                'database' => '::database::',
            ],
        ]);
    }


    /** @test */
    public function modelShowsAnErrorWhenNoApiKey()
    {
        $this->artisan('recon')
            ->expectsOutput('Missing API Key. Add RECON_TOKEN={apiKey} to your .env file. You can get a key at https://reconengine.ai')
            ->assertExitCode(1);
    }

    // 1.a.
    /** @test */
    public function promptSetDatabaseEnvWhenNonePresentWithRemoteResults()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => null,
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsChoice('No local database set. Would you like to use an existing database?', '::database1::', ['::database1::', '::database2::'])
            ->expectsQuestion("Which action would you like to perform on '::database1::'?", 'Nevermind, just lookin\'')
            ->assertExitCode(0)
        ;

        // TODO: assert .env file is written to.
    }

    // 1.b. missing remote, do you want to create local?
    /** @test */
    public function promptCreateDatabaseFromUserInputWhenDoesNotExist()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [],
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsConfirmation("Would you like to create database: '::database::'?", 'Yes')
            ->expectsQuestion("Which action would you like to perform on '::database::'?", 'Nevermind, just lookin\'')
            ->assertExitCode(0)
        ;

        Http::assertSent(function (Request $request) {
            return Str::contains($request->url(), ['/api/databases'])
                && $request->method() === 'POST'
                && $request['name'] === '::database::';
        });
    }

    // 2.a. missing local, no remotes.
    /** @test */
    public function promptCreateDatabaseWhenDoesNotExist()
    {

        config([
            'recon' => [
                'token' => 'abc',
                'database' => null,
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [],
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsQuestion("Not local database set. Let's make you a new one. What would you like to call it (alphanumeric, '-', or '_')?", '::database::')
            ->expectsConfirmation("Would you like to create database: '::database::'?", 'Yes')
            ->expectsQuestion("Which action would you like to perform on '::database::'?", 'Nevermind, just lookin\'')
            ->assertExitCode(0)
        ;

        Http::assertSent(function (Request $request) {
            return Str::contains($request->url(), ['/api/databases'])
                && $request->method() === 'POST'
                && $request['name'] === '::database::'
                && $request['user_schema'] === (new TestModelUser())->getReconDefinition()->toJson()
                && $request['item_schema'] === (new TestModelItem())->getReconDefinition()->toJson();
        });

        // TODO: assert .env file was written to.
    }

    // 2.b. All is right.
    /** @test */
    public function allDatabasesAreDisplayed()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database1::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsOutput('**  1. ::database1:: (default)')
            ->expectsQuestion("Which action would you like to perform on '::database1::'?", 'Nevermind, just lookin\'')
            ->expectsOutput('We appreciate you 👋')
            ->assertExitCode(0)
        ;
    }

    // warning if no remote databases match with the locally set one.
    /** @test */
    public function warningWhenConfigDatabaseDoesNotExistInRemote()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database_not_in_http_response::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsOutput("Database '::database_not_in_http_response::' does not exist at https://reconengine.ai.")
            ->expectsQuestion("Would you like to create database: '::database_not_in_http_response::'?", 'Y')
            ->expectsQuestion("Which action would you like to perform on '::database_not_in_http_response::'?", 'Nevermind, just lookin\'')
            ->assertExitCode(0)
        ;
    }

    /** @test */
    public function allDatabaseParamFromUserInputWillOverrideDefaultDatabase()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database1::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
        ]);

        $this->artisan('recon --database=::database2::')
            ->expectsOutput('**  2. ::database2:: (default)')
            ->expectsQuestion("Which action would you like to perform on '::database2::'?", 'Nevermind, just lookin\'')
            ->assertExitCode(0)
        ;
    }

    /** @test */
    public function deleteDatabaseIsNotSupportedOnCli()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database1::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsOutput('**  1. ::database1:: (default)')
            ->expectsQuestion("Which action would you like to perform on '::database1::'?", 'Delete')
            ->expectsOutput('Deleting a database from the command line is currently not supported. Please delete at https://reconengine.ai.')
            ->assertExitCode(0)
        ;
    }

    /** @test */
    public function seedDatabaseWillCallSeedCommand()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database1::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
        ]);

        $this->artisan('recon')
            ->expectsOutput('**  1. ::database1:: (default)')
            ->expectsQuestion("Which action would you like to perform on '::database1::'?", 'Seed')
            ->assertExitCode(0)
        ;
    }

    /** @test */
    public function retrainDatabaseWillCallRetrainCommand()
    {
        config([
            'recon' => [
                'token' => 'abc',
                'database' => '::database1::',
            ],
        ]);

        Http::fake([
            Api::HOST . '/databases' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => '::database1::',
                    ],
                    [
                        'id' => 2,
                        'name' => '::database2::',
                    ],
                ]
            ], 200),
            Api::HOST . '/databases/::database1::' => Http::response([], 200),
        ]);

        $this->artisan('recon')
            ->expectsOutput('**  1. ::database1:: (default)')
            ->expectsQuestion("Which action would you like to perform on '::database1::'?", 'Retrain')
            ->assertExitCode(0)
        ;
    }
}
