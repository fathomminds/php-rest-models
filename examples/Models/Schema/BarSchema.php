<?php
namespace Fathomminds\Clurexid\Rest\Examples\Models\Schema;

use Fathomminds\Clurexid\Rest\Schema\SchemaValidator;
use Fathomminds\Clurexid\Rest\Examples\Models\Schema\TypeValidators\PhoneNumberValidator;

class BarSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
            'validator' => [
                'class' => StringValidator::class,
            ]
        ],
        'mobile' => [
            'validator' => [
                'class' => PhoneNumberValidator::class,
            ]
        ]
    ];
}
