<?php


namespace LaravelMl\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\MlModel;
use LaravelMl\MlModelConfig;

class TestModel extends Model
{
    use MlModel;

    public $isTrainable = true;

    protected $fillable = [
        'name',
        'age',
        'salary',
    ];

    protected $casts = [
        'age' => 'int',
        'salary' => 'int',
    ];

    public function features(): array
    {
        return [
            $this->age,
        ];
    }

    public function label()
    {
        return $this->salary;
    }

    protected function config(MlModelConfig $config)
    {
        return $config
            ->setName('test_models')
            ->setId($this->id)
            ->setType(MlModelConfig::TYPE_CONTINUOUS)
            ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS)
        ;
    }

    /**
     * @return bool
     */
    public function isTrainable(): bool
    {
        return $this->isTrainable;
    }
}
