<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\CastExample;

use Fathomminds\Rest\Schema;

class ZSchema extends Schema
{
    public function schema()
    {
        return [
            'a' => [
                'validator' => [
                    'class' => Schema\TypeValidators\StringValidator::class,
                ],
            ],
            'b' => [
                'type' => 'schema',
                'default' => YSchema::cast((object)[]),
                'validator' => [
                    'class' => YSchema::class,
                ],
            ],
            'c' => [
                'validator' => [
                    'class' => Schema\TypeValidators\IntegerValidator::class,
                    'params' => [
                        'nullable' => true,
                    ],
                ],
            ],
        ];
    }
}
