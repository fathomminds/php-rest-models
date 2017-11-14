<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 * Class OnePk
 * @package Fathomminds\Rest\Examples\Clusterpoint\Models\Schema
 *
 * @property string _id
 * @property string value
 */

class OnePKSchema extends Schema
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
            'value' => [
                'validator' => [
                    'class' => StringValidator::class,
                ],
            ],
        ];
    }
}
