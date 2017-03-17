<?php
namespace Fathomminds\Clurexid\Rest\Examples\Models\Schema;

use Fathomminds\Clurexid\Rest\Schema\SchemaValidator;
use Fathomminds\Clurexid\Rest\Schema\TypeValidators\StringValidator;

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
