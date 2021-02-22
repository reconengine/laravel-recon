<?php


namespace LaravelMl\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use LaravelMl\Helpers\SchemaDefinition;
use LaravelMl\LmlItem;

class TestModelItem extends Model
{
    use LmlItem;

    public $isTrainable = true;

    protected $fillable = [
        'color',
        'rating',
        'ratings',
    ];

    protected $casts = [
        'ratings' => 'int',
        'rating' => 'double',
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
        $definition->category('color');
        $definition->double('rating');
        $definition->int('ratings');
    }
}
