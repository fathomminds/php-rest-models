<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 * Class OnePk
 * @package Fathomminds\Rest\Examples\Clusterpoint\Models\Schema
 *
 * @property string _id
 * @property string uniqueValue
 * @property string nonUniqueValue
 */

class OneUniqueFieldSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                ],
            ],
            'uniqueValue' => [
                'unique' => true,
                'default' => null,
                'validator' => [
                    'class' => StringValidator::class,
                    'params' => [
                        'nullable' => true,
                    ],
                ],
            ],
            'nonUniqueValue' => [
                'default' => null,
                'validator' => [
                    'class' => StringValidator::class,
                    'params' => [
                        'nullable' => true,
                    ],
                ],
            ],
        ];
    }
}
