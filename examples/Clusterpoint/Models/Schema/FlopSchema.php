<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\DoubleValidator;

/**
 *
 * @property string $_id
 * @property string $mobile
 *
 */

class FlopSchema extends Schema
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
                    'class' => StringValidator::class,
                ]
            ],
            'addrLat' => [
                'validator' => [
                    'class' => DoubleValidator::class,
                ]
            ],
            'addrLng' => [
                'validator' => [
                    'class' => DoubleValidator::class,
                ]
            ],
        ];
    }
}
