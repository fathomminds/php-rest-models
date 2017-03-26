<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\BarSchema;

/**
 *
 * @property string $_id
 * @property string $title
 * @property string $other
 * @property string $noindex
 * @property integer $status
 * @property BarSchema $bar
 *
 */

class FooSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'title' => [
                'unique' => true,
                'required' => true,
                'validator' => [
                    'class' => StringValidator::class,
                    'params' => [
                        'maxLength' => 100,
                    ],
                ],
            ],
            'other' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                    'params' => [
                        'maxLength' => 100,
                    ],
                ],
            ],
            'noindex' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                    'params' => [
                        'maxLength' => 100,
                    ],
                ],
            ],
            'status' => [
                'validator' => [
                    'class' => IntegerValidator::class,
                    'params' => [
                        'min' => 0,
                        'max' => 1,
                    ],
                ],
            ],
            'bar' => [
                'type' => BarSchema::class,
            ],
        ];
    }
}
