<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\CastExample;

use Fathomminds\Rest\Schema;

class YSchema extends Schema
{
    public function schema()
    {
        return [
            'd' => [
                'validator' => [
                    'class' => Schema\TypeValidators\StringValidator::class,
                    'params' => [
                        'nullable' => true,
                    ],
                ],
            ],
            'e' => [
                'validator' => [
                    'class' => Schema\TypeValidators\IntegerValidator::class,
                    'params' => [
                        'nullable' => true,
                    ],
                ]
            ],
        ];
    }
}
