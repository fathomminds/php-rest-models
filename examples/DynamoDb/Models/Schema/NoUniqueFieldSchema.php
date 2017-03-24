<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

class NoUniqueFieldSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
            'validator' => [
                'class' => StringValidator::class,
            ]
        ],
        'multi' => [
            'validator' => [
                'class' => StringValidator::class,
            ]
        ]
    ];
}
