<?php


namespace Recon\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use Recon\Helpers\SchemaDefinition;
use Recon\Models\ReconUser;

class TestModelUser extends Model
{
    use ReconUser;

    public $isTrainable = true;

    protected $fillable = [
        'name',
        'gender',
        'age',
        'salary',
    ];

    protected $casts = [
        'age' => 'int',
        'salary' => 'int',
    ];

    /**
     * @return bool
     */
    public function isTrainable(): bool
    {
        return $this->isTrainable;
    }

    protected function define(SchemaDefinition $definition)
    {
        $definition->category('gender');
        $definition->int('age')->nullable();
        $definition->int('salary');
    }
}
