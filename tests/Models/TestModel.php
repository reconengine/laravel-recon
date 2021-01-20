<?php


namespace LaravelMl\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\MlModel;
use LaravelMl\MlModelConfig;

class TestModel extends Model
{
    use MlModel;

    protected $fillable = [
        'name',
        'age',
        'salary',
    ];

    protected $casts = [
        'age' => 'int',
        'salary' => 'int',
    ];

    protected function features(): array
    {
        return [
            $this->name,
            $this->age,
        ];
    }

    protected function label()
    {
        return $this->salary;
    }

    protected function config(): MlModelConfig
    {
        return MlModelConfig::make()
            ->setName('test_models')
            ->setId($this->id)
            ->setType(MlModelConfig::TYPE_CONTINUOUS)
        ;
    }
}
