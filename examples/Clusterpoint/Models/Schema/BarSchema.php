<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\TypeValidators\PhoneNumberValidator;

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