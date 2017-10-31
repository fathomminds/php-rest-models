<?php
namespace Fathomminds\Rest;

use Fathomminds\Rest\Contracts\ISchema;
use Fathomminds\Rest\Schema\SchemaValidator;
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
        $schema = $this->schema();
        foreach (get_object_vars($object) as $name => $value) {
            $this->{$name} = $this->castProperty($schema, $name, $value);
        }
    }

    private function castProperty($schema, $name, $value)
    {
        if (!array_key_exists($name, $schema)) {
            return $value;
        }
        if (isset($schema[$name]['type']) && $schema[$name]['type'] === 'schema') {
            return $schema[$name]['validator']['class']::cast($value);
        }
        $params = empty($schema[$name]['validator']['params'])
            ? null
            : $schema[$name]['validator']['params'];
        return $schema[$name]['validator']['class']::cast($value, $params);
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

    public static function cast($object)
    {
        return new static($object);
    }

    public function setFieldDefaults()
    {
        $properties = array_diff_key(
            (new SchemaValidator(static::class))->getFieldsWithDefaults($this),
            get_object_vars($this)
        );
        foreach ($properties as $fieldName => $field) {
            $this->setFieldDefaultValue($fieldName, $field['default']);
        }
        return $this;
    }

    protected function setFieldDefaultValue($fieldName, $value)
    {
        if (gettype($value) === 'object' && is_callable($value)) {
            $this->{$fieldName} = $value();
            return;
        }
        $this->{$fieldName} = $value;
    }

    public function validate()
    {
        (new SchemaValidator(static::class))->validate($this);
        return $this;
    }
}
