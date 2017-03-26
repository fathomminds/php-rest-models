<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 * 
 * @property string $id
 * @property string $multi
 *
 */
class NoUniqueFieldSchema extends Schema
{
    public function schema()
    {
        return [
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
}
