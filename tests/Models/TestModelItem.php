<?php


namespace Recon\Tests\Models;


use Illuminate\Database\Eloquent\Model;
use Recon\Helpers\SchemaDefinition;
use Recon\ReconItem;

class TestModelItem extends Model
{
    use ReconItem;

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
