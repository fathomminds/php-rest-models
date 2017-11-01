<?php
namespace Fathomminds\Rest;

use Fathomminds\Rest\Contracts\ISchema;
use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Exceptions\RestException;

abstract class Schema implements ISchema
{
    const REPLACE_MODE = 'replace';
    const UPDATE_MODE = 'update';

    public static function cast($object)
    {
        return new static($object);
    }

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

    public function setFieldDefaults()
    {
        $schemaValidator = new SchemaValidator(static::class);
        $schemaFields = $schemaValidator->getSchemaFieldsWithDetails($this);
        $defaultFields = $schemaValidator->getFieldsWithDefaults($this);
        $this->setNestedFieldDefaults($schemaFields);
        $this->setRemainingFieldDefaults($defaultFields);
        return $this;
    }

    protected function setNestedFieldDefaults($schemaFields)
    {
        foreach ($schemaFields as $schemaFieldName => $schemaFieldDetails) {
            $propertyExists = property_exists($this, $schemaFieldName);
            if (isset($schemaFieldDetails['default']) && !$propertyExists) {
                $this->setFieldDefaultValue($schemaFieldName, $schemaFieldDetails['default']);
                $propertyExists = true;
            }
            if ($propertyExists) {
                $this->{$schemaFieldName}->setFieldDefaults();
            }
        }
    }

    protected function setRemainingFieldDefaults($defaultFields)
    {
        $properties = array_diff_key(
            $defaultFields,
            get_object_vars($this)
        );
        foreach ($properties as $fieldName => $field) {
            $this->setFieldDefaultValue($fieldName, $field['default']);
        }
    }

    protected function setFieldDefaultValue($fieldName, $value)
    {
        if (gettype($value) === 'object' && is_callable($value)) {
            $this->{$fieldName} = $value();
            return;
        }
        $this->{$fieldName} = $value;
    }

    public function validate($mode = null)
    {
        $schemaValidator = new SchemaValidator(static::class);
        switch ($mode) {
            case self::REPLACE_MODE:
                $schemaValidator->replaceMode(true);
                $schemaValidator->updateMode(false);
                break;
            case self::UPDATE_MODE:
                $schemaValidator->replaceMode(false);
                $schemaValidator->updateMode(true);
                break;
            default:
                break;
        }
        $schemaValidator->validate($this);
        return $this;
    }
}
