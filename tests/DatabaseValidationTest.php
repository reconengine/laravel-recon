<?php

namespace LaravelMl\Tests;

use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\LmlDatabaseConfig;
use LaravelMl\LmlRecordConfig;
use LaravelMl\Tests\Models\MockMlTestModel;

class DatabaseValidationTest extends BaseTest
{
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Features
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /** @test */
    public function featuresMustMatchDatatype()
    {
        config([
            'laravel-ml' => [
                'databases' => [
                    LmlDatabaseConfig::make()
                        ->setName('test_models')
                        ->setType(LmlDatabaseConfig::TYPE_CONTINUOUS)
                        ->setDatatype(LmlDatabaseConfig::DATATYPE_CONTINUOUS)
                ]
            ],
        ]);

        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(LmlRecordConfig $config)
            {
                $config->setDatabase('test_models');
            }
        };

        // no exception
        $testModel->ml()->validateFeatures([
            44.0,
            45.0,
        ]);
        $this->expectException(DatatypeMismatchException::class);
        // exception
        $testModel->ml()->validateFeatures([
            'green',
            45.0,
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Label
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /** @test */
    public function labelMustMatchDatatype()
    {
        config([
            'laravel-ml' => [
                'databases' => [
                    LmlDatabaseConfig::make()
                        ->setName('test_models')
                        ->setType(LmlDatabaseConfig::TYPE_CONTINUOUS)
                        ->setDatatype(LmlDatabaseConfig::DATATYPE_CONTINUOUS)
                ]
            ],
        ]);


        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(LmlRecordConfig $config)
            {
                $config->setDatabase('test_models');
            }
        };


        // no exception
        $testModel->ml()->validateLabel(45.0);
        $this->expectException(DatatypeMismatchException::class);
        // exception
        $testModel->ml()->validateLabel('green');
    }

    /** @test */
    public function labelRequired()
    {
        config([
            'laravel-ml' => [
                'databases' => [
                    LmlDatabaseConfig::make()
                        ->setName('test_models')
                        ->setType(LmlDatabaseConfig::TYPE_CONTINUOUS)
                        ->setDatatype(LmlDatabaseConfig::DATATYPE_CONTINUOUS)
                ]
            ],
        ]);

        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(LmlRecordConfig $config)
            {
                $config->setDatabase('test_models');
            }
        };


        // no exception
        $testModel->ml()->validateLabel(45.0);
        $this->expectException(DatatypeMismatchException::class);
        // exception
        $testModel->ml()->validateLabel(null);
    }

    /** @test */
    public function labelNotRequiredForAnomaly()
    {
        config([
            'laravel-ml' => [
                'databases' => [
                    LmlDatabaseConfig::make()
                        ->setName('test_models')
                        ->setType(LmlDatabaseConfig::TYPE_ANOMALY)
                        ->setDatatype(LmlDatabaseConfig::DATATYPE_CONTINUOUS)
                ]
            ],
        ]);

        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(LmlRecordConfig $config)
            {
                $config->setDatabase('test_models');
            }
        };

        // no exception
        $testModel->ml()->validateLabel(45.0);
        // exception
        $testModel->ml()->validateLabel(null);

        // no exception thrown ^
        $this->assertTrue(true);
    }
}
