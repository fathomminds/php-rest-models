<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;

class FooSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
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
