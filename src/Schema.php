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

    public static function castWithoutExtraneous($object)
    {
        if (gettype($object) !== 'object') {
            throw new RestException('Schema castWithoutExtraneous method expects object as parameter', [
                'parameter' => $object,
            ]);
        }
        return new static($object, ['withoutExtraneous' => true]);
    }

    public static function castByMap($object, $map)
    {
        if (gettype($object) !== 'object') {
            throw new RestException('Schema castByMap method expects object as first parameter', [
                'parameter' => $object,
            ]);
        }
        if (!is_array($map)) {
            throw new RestException('Schema castByMap method expects array as second parameter', [
                'parameter' => $map,
            ]);
        }
        return new static($object, ['map' => $map]);
    }

    public function __construct($object = null, $params = [])
    {
        if ($object === null) {
            return;
        }
        if (gettype($object) !== 'object') {
            throw new RestException('Schema constructor expects object or null as parameter', [
                'parameter' => $object,
            ]);
        }
        $this->castProperties($object, $this->schema(), $params);
    }

    private function castProperties($object, $schema, $params)
    {
        if (isset($params['withoutExtraneous'])) {
            $this->castPropertiesWithoutExtraneous($object, $schema);
            return;
        }
        if (isset($params['map'])) {
            $this->castPropertiesByMap($schema, $object, $params['map']);
            return;
        }
        $this->castPropertiesDefault($object, $schema);
    }

    private function castPropertiesWithoutExtraneous($object, $schema)
    {
        foreach (get_object_vars($object) as $name => $value) {
            list($propertyExists, $propertyValue) = $this->castPropertyWithoutExtraneous($schema, $name, $value);
            if ($propertyExists) {
                $this->{$name} = $propertyValue;
            }
        }
    }

    private function castPropertyWithoutExtraneous($schema, $name, $value)
    {
        if (!array_key_exists($name, $schema)) {
            return [false, null];
        }
        if (isset($schema[$name]['type']) && $schema[$name]['type'] === 'schema') {
            return [true, $schema[$name]['validator']['class']::castWithoutExtraneous($value)];
        }
        $params = empty($schema[$name]['validator']['params'])
            ? null
            : $schema[$name]['validator']['params'];
        return [true, $schema[$name]['validator']['class']::cast($value, $params)];
    }

    private function castPropertiesByMap($schema, $object, $map)
    {
        $mappedObject = new \StdClass();
        foreach ($map as $targetFieldName => $sourceFieldName) {
            list($propertyExists, $propertyValue) = $this->getPropertyValue($object, $sourceFieldName);
            if ($propertyExists) {
                $mappedObject = $this->setPropertyValue(
                    $mappedObject,
                    $targetFieldName,
                    $propertyValue
                );
            }
        }
        $this->castPropertiesWithoutExtraneous($mappedObject, $schema);
    }

    private function setPropertyValue($mappedObject, $targetFieldName, $propertyValue)
    {
        $fieldNameArr = explode('.', $targetFieldName);
        $fieldName = array_shift($fieldNameArr);
        if (count($fieldNameArr) !== 0) {
            if (!property_exists($mappedObject, $fieldName)) {
                $mappedObject->{$fieldName} = new \StdClass();
            }
            $mappedObject->{$fieldName} = $this->setPropertyValue(
                $mappedObject->{$fieldName},
                implode('.', $fieldNameArr),
                $propertyValue
            );
            return $mappedObject;
        }
        $mappedObject->{$fieldName} = $propertyValue;
        return $mappedObject;
    }

    private function getPropertyValue($object, $sourceFieldName)
    {
        $fieldNameArr = explode('.', $sourceFieldName);
        $fieldName = array_shift($fieldNameArr);
        if (!property_exists($object, $fieldName)) {
            return [
                false,
                null
            ];
        }
        if (count($fieldNameArr) === 0) {
            return [
                true,
                json_decode(json_encode($object->{$fieldName}))
            ];
        }
        return $this->getPropertyValue($object->{$fieldName}, implode('.', $fieldNameArr));
    }

    private function castPropertiesDefault($object, $schema)
    {
        foreach (get_object_vars($object) as $name => $value) {
            $this->{$name} = $this->castPropertyDefault($schema, $name, $value);
        }
    }

    private function castPropertyDefault($schema, $name, $value)
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
