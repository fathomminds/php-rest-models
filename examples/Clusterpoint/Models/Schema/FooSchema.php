<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;
use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Fathomminds\Rest\Helpers\Uuid;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\BarSchema;

/**
 *
 * @property string $_id
 * @property string $title
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
                'default' => function () {
                    return (new Uuid)->generate();
                },
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
            'status' => [
                'default' => 0,
                'validator' => [
                    'class' => IntegerValidator::class,
                    'params' => [
                        'min' => 0,
                        'max' => 1,
                    ],
                ],
            ],
            'bar' => [
                'validator' => [
                    'class' => ArrayValidator::class,
                    'params' => [
                        'key' => [
                            'validator' => [
                                'class' => IntegerValidator::class,
                            ],
                        ],
                        'item' => [
                            'type' => 'schema',
                            'validator' => [
                                'class' => BarSchema::class,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
