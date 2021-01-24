<?php

namespace LaravelMl\Tests;

use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\MlModelConfig;
use LaravelMl\Tests\Models\MockMlTestModel;

class ModelValidationTest extends BaseTest
{
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Features
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /** @test */
    public function featuresMustMatchDatatype()
    {
        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(MlModelConfig $config)
            {
                $config->setType(MlModelConfig::TYPE_CONTINUOUS)
                    ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
                ;
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
        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(MlModelConfig $config)
            {
                $config->setType(MlModelConfig::TYPE_CONTINUOUS)
                    ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
                ;
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
        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(MlModelConfig $config)
            {
                $config->setType(MlModelConfig::TYPE_CONTINUOUS)
                    ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
                ;
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
        $testModel = new class extends MockMlTestModel {
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(MlModelConfig $config)
            {
                $config->setType(MlModelConfig::TYPE_ANOMALY)
                    ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
                ;
            }
        };

        // no exception
        $testModel->ml()->validateLabel(45.0);
        // exception
        $testModel->ml()->validateLabel(null);

        // no exception thrown ^
        $this->assertTrue(true);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Config
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /** @test */
    public function configDefaultsToModelId()
    {
        $testModel = new class extends MockMlTestModel {
            public $id = '::id::';
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(MlModelConfig $config)
            {
                $config->setType(MlModelConfig::TYPE_CONTINUOUS)
                    ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
                ;
            }
        };

        $this->assertEquals('::id::', $testModel->ml()->id());
    }

    /** @test */
    public function configModelIdIsUsedWhenSet()
    {
        $testModel = new class extends MockMlTestModel {
            public $id = '::id::';
            public function features(): array{return [45.0, 90.0, 100.0];}
            public function label(){return 45;}
            protected function config(MlModelConfig $config)
            {
                $config->setType(MlModelConfig::TYPE_CONTINUOUS)
                    ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
                    ->setId($this->id . 'custom')
                ;
            }
        };

        $this->assertEquals('::id::custom', $testModel->ml()->id());
    }
}
