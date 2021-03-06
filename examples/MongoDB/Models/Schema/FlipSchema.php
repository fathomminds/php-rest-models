<?php
namespace Fathomminds\Rest\Examples\MongoDB\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 *
 * @property string $_id
 * @property string $name
 * @property string $email
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
