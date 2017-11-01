<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;

class FruitSchema extends Schema
{
    public function schema()
    {
        return [
            'seed' => [
                'type' => 'schema',
                'validator' => [
                    'class' => SeedSchema::class,
                ]
            ],
        ];
    }
}
