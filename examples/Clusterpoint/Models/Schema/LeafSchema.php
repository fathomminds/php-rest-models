<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

class LeafSchema extends Schema
{
    public function schema()
    {
        return [
            'vegetable' => [
                'type' => 'schema',
                'validator' => [
                    'class' => VegetableSchema::class,
                ]
            ],
        ];
    }
}
