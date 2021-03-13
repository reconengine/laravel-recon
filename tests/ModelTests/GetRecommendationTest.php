<?php


namespace Recon\Tests\ModelTests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Recon\Api\Api;
use Recon\Helpers\SchemaDefinition;
use Recon\ReconItem;
use Recon\Tests\BaseTest;
use Recon\Tests\Models\TestModelItem;
use Recon\Tests\Models\TestModelUser;

class GetRecommendationTest extends BaseTest
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
    public function isReconItem()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    'recommendation_id' => '::recommendation_id::',
                    'items' => [
                        [
                            'item_id' => "::item1::",
                            'score' => 0.09,
                        ],
                        [
                            'item_id' => "::item2::",
                            'score' => 0.05,
                        ],
                    ],
                ],
            ])
        ]);

        $testModel = TestModelItem::create([
            'color' => 'green',
            'rating' => 4.45,
            'ratings' => 132,
        ]);

        $actualRelatedItemResponse = $testModel->related();

        $this->assertEquals([
            'recommendation_id' => '::recommendation_id::',
            'items' => [
                [
                    'item_id' => "::item1::",
                    'score' => 0.09,
                ],
                [
                    'item_id' => "::item2::",
                    'score' => 0.05,
                ],
            ],
        ], $actualRelatedItemResponse);
    }

    /** @test */
    public function isReconUser()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    'recommendation_id' => '::recommendation_id::',
                    'items' => [
                        [
                            'item_id' => "::item1::",
                            'score' => 0.09,
                        ],
                        [
                            'item_id' => "::item2::",
                            'score' => 0.05,
                        ],
                    ],
                ],
            ])
        ]);

        $testModel = TestModelUser::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'age' => 22,
            'salary' => 124567,
        ]);

        $actualRecommendedItemsResponse = $testModel->recommend();

        $this->assertEquals([
            'recommendation_id' => '::recommendation_id::',
            'items' => [
                [
                    'item_id' => "::item1::",
                    'score' => 0.09,
                ],
                [
                    'item_id' => "::item2::",
                    'score' => 0.05,
                ],
            ],
        ], $actualRecommendedItemsResponse);
    }

    public function testSchemaCasting()
    {
        $test = new class extends Model {
            use ReconItem;

            protected function define(SchemaDefinition $definition) {
                $definition->boolean('::boolean::');
                $definition->int('::int::');
                $definition->long('::long::');
                $definition->double('::double::');
                $definition->float('::float::');
                $definition->string('::string::');
                $definition->category('::category::');
            }
        };

        $longCarbon = now();

        $test->forceFill([
            '::boolean::' => 1,
            '::int::' => '45',
            '::long::' => $longCarbon,
            '::double::' => 1234.432,
            '::float::' => '432.43',
            '::string::' => 34,
            '::category::' => 'red',
        ]);

        $json = $test->toReconJson();

        $this->assertSame([
            '::boolean::' => true,
            '::int::' => 45,
            '::long::' => $longCarbon->timestamp,
            '::double::' => 1234.432,
            '::float::' => 432.43,
            '::string::' => '34',
            '::category::' => 'red',
        ], $json);
    }
}
