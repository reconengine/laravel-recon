<?php


namespace LaravelMl\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\LmlRecord;
use LaravelMl\LmlDatabaseConfig;

abstract class MockMlTestModel
{
    use LmlRecord;

    public $id = 1;
}
