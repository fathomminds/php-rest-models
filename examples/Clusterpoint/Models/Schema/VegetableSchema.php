<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;

class VegetableSchema extends Schema
{
    public function schema()
    {
        return [
            'name' => [
                'type' => 'schema',
                'validator' => [
                    'class' => Schema\TypeValidators\StringValidator::class,
                ]
            ],
        ];
    }
}
