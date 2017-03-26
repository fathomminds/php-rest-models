<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\TypeValidators\PhoneNumberValidator;

class BarSchema extends Schema
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
            'mobile' => [
                'validator' => [
                    'class' => PhoneNumberValidator::class,
                ]
            ]
        ];
    }
}
