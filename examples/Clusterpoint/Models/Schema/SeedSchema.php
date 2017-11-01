<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

class SeedSchema extends Schema
{
    public function schema()
    {
        return [
            'tree' => [
                'type' => 'schema',
                'validator' => [
                    'class' => TreeSchema::class,
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
