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

    public function __get($name)
    {
        if (!isset($this->{$name})) {
            throw new RestException(
                'Trying to access undefined property ' . $name,
                []
            );
        }
        return $this->{$name};
    }

    abstract public function schema();

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}
