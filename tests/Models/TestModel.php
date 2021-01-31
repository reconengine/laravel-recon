<?php


namespace LaravelMl\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\LmlRecord;
use LaravelMl\LmlDatabaseConfig;
use LaravelMl\LmlRecordConfig;

class TestModel extends Model
{
    use LmlRecord;

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

    protected function config(LmlRecordConfig $config)
    {
        return $config
            ->setDatabase('test_models')
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
