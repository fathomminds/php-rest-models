<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 *
 * @property string $_id
 * @property string $flop
 *
 */

class BarSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'flip' => [
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'flop' => [
                'type' => 'schema',
                'validator' => [
                    'class' => FlopSchema::class,
                ],
            ],
        ];
    }
}
