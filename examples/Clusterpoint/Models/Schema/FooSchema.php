<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;
use Fathomminds\Rest\Helpers\Uuid;

class FooSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
            'validator' => [
                'class' => StringValidator::class,
            ]
        ],
        'title' => [
            'unique' => true,
            'required' => true,
            'validator' => [
                'class' => StringValidator::class,
                'params' => [
                    'maxLength' => 100,
                ],
            ],
        ],
        'status' => [
            'default' => 0,
            'validator' => [
                'class' => IntegerValidator::class,
                'params' => [
                    'min' => 0,
                    'max' => 1,
                ],
            ],
        ],
        'bar' => [
            'type' => BarSchema::class,
        ],
    ];

    public function __construct()
    {
        $this->setDefault('_id', function () {
            return (new Uuid)->generate();
        });
    }
}
