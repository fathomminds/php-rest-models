<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 *
 * @property string $_id
 * @property string $mobile
 *
 */

class FlipSchema extends Schema
{
    public function schema()
    {
        return [
            'title' => [
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'name' => [
                'default' => 'Default Flip Name',
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'email' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
    }
}
