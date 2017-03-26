<?php
namespace Fathomminds\Rest;

use Fathomminds\Rest\Contracts\ISchema;
use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Exceptions\RestException;

abstract class Schema implements ISchema
{
    public function __construct($object = null)
    {
        if ($object === null) {
            return;
        }
        if (gettype($object) !== 'object') {
            throw new RestException('Schema constructor expects object or null as parameter', [
                'parameter' => $object,
            ]);
        }
        foreach (get_object_vars($object) as $name => $value) {
            $this->{$name} = $value;
        }
    }

    abstract public function schema();
}
