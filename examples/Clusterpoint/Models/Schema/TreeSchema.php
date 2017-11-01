<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;

class TreeSchema extends Schema
{
    public function schema()
    {
        return [
            'fruit' => [
                'type' => 'schema',
                'validator' => [
                    'class' => FruitSchema::class,
                ]
            ],
            'leaf' => [
                'type' => 'schema',
                'validator' => [
                    'class' => LeafSchema::class,
                ]
            ],
        ];
    }
}
