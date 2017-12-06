<?php
namespace Fathomminds\Rest\Examples\MongoDB\Models\Schema;

use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FlipSchema;
use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;
use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Fathomminds\Rest\Helpers\Uuid;

/**
 *
 * @property string $_id
 * @property string $title
 * @property integer $status
 * @property BarSchema $bar
 * @property FlipSchema $flip
 * @property BooSchema $boo
 *
 */


class FooSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'unique' => true,
                'default' => function () {
                    return (new Uuid)->generate();
                },
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'title' => [
                'unique' => true,
                'required' => true,
                'default' => function () {
                    return (isset($this->flip) && isset($this->flip->title))
                        ? 'Title for flip: ' . $this->flip->title
                        : 'Title for flip: Default Title';
                },
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
                'validator' => [
                    'class' => ArrayValidator::class,
                    'params' => [
                        'key' => [
                            'validator' => [
                                'class' => IntegerValidator::class,
                            ],
                        ],
                        'item' => [
                            'type' => 'schema',
                            'validator' => [
                                'class' => BarSchema::class,
                            ],
                        ],
                    ],
                ],
            ],
            'flip' => [
                'type' => 'schema',
                'validator' => [
                    'class' => FlipSchema::class,
                ]
            ],
            'boo' => [
                'type' => 'schema',
                'default' => function() {
                    return new BooSchema(BooSchema::cast((object)[
                        'title' => 'test',
                        'email' => 'test@test.hu'
                    ]));
                },
                'validator' => [
                    'class' => BooSchema::class,
                ]
            ],
        ];
    }
}
