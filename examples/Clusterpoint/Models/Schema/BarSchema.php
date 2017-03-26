<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\TypeValidators\PhoneNumberValidator;

/**
 * 
 * @property string $_id
 * @property string $mobile
 */

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
