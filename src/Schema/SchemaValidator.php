<?php
namespace Fathomminds\Rest\Schema;

use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Exceptions\RestException;

class SchemaValidator
{
    protected $fields = [];
    protected $allowExtraneous = false;
    protected $requiredSchemaClass = null;
    private $updateMode = false;
    private $replaceMode = false;

    public function __construct($requiredSchemaClass = null)
    {
        $this->requiredSchemaClass = $requiredSchemaClass;
    }

    public function updateMode($updateMode = null)
    {
        if (is_bool($updateMode)) {
            $this->updateMode = $updateMode;
        }
        return $this->updateMode;
    }

    public function replaceMode($replaceMode = null)
    {
        if (is_bool($replaceMode)) {
            $this->replaceMode = $replaceMode;
        }
        return $this->replaceMode;
    }

    public function validate($resource)
    {
        $this->validateResourceType($resource);
        $extraneousCheck = [];
        if (!$this->allowExtraneous) {
            $extraneousCheck = $this->validateExtraneousFields($resource);
        }
        $errors = array_merge(
            $this->validateRequiredFields($resource),
            $extraneousCheck,
            $this->validateFieldTypes($resource)
        );
        if (!empty($errors)) {
            throw new RestException(
                'Invalid structure',
                [
                    'schema' => get_class($resource),
                    'errors' => $errors,
                ]
            );
        }
    }

    public function allowExtraneous($value)
    {
        $this->allowExtraneous = $value;
    }

    private function validateResourceType($resource)
    {
        $this->expectObject($resource);
        $this->objectHasSchemaMethod($resource);
        $this->objectIsValidSchemaClass($resource);
    }

    private function expectObject($resource)
    {
        if (gettype($resource) !== 'object') {
            throw new RestException(
                'Object expected',
                [
                    'schema' => static::class,
                    'type' => gettype($resource),
                ]
            );
        }
    }

    private function objectHasSchemaMethod($resource)
    {
        if (!method_exists($resource, 'schema')) {
            throw new RestException(
                'Object must be a correct RestSchema object',
                [
                    'schema' => static::class,
                    'type' => gettype($resource),
                ]
            );
        }
    }

    private function objectIsValidSchemaClass($resource)
    {
        if ($this->requiredSchemaClass === null) {
            return;
        }
        if (get_class($resource) !== $this->requiredSchemaClass) {
            throw new RestException(
                'Object must be an instance of the defined SchemaClass',
                [
                    'schema' => static::class,
                    'type' => gettype($resource),
                ]
            );
        }
    }

    private function validateRequiredFields($resource)
    {
        $errors = [];
        if ($this->updateMode()) {
            return $errors;
        }
        $missingFields = array_diff($this->getRequiredFields($resource), array_keys(get_object_vars($resource)));
        array_walk($missingFields, function($item) use (&$errors) {
            $errors[$item] = 'Missing required field';
        });
        return $errors;
    }

    private function validateExtraneousFields($resource)
    {
        $errors = [];
        $extraFields = array_diff(array_keys(get_object_vars($resource)), array_keys($resource->schema()));
        array_walk($extraFields, function($item) use (&$errors) {
            $errors[$item] = 'Extraneous field';
        });
        return $errors;
    }

    private function validateFieldTypes($resource)
    {
        $validatorFactory = new ValidatorFactory;
        $errors = [];
        foreach ($resource->schema() as $fieldName => $rules) {
            if (property_exists($resource, $fieldName)) {
                try {
                    $validatorFactory
                        ->create(
                            $rules,
                            $this->updateMode(),
                            $this->replaceMode()
                        )
                        ->validate(
                            $resource->{$fieldName}
                        );
                } catch (RestException $ex) {
                    $errors[$fieldName]['error'] = $ex->getMessage();
                    $errors[$fieldName]['details'] = $ex->getDetails();
                }
            }
        }
        return $errors;
    }

    private function filterFields($resource, $paramKey, $paramValue, $checkParamValue = true)
    {
        $fields = [];
        foreach ($resource->schema() as $fieldName => $params) {
            if (array_key_exists($paramKey, $params) && $this->isMatchedValue(
                $checkParamValue,
                $params[$paramKey],
                $paramValue
            )) {
                $fields[$fieldName] = $params;
            }
        }
        return $fields;
    }

    private function isMatchedValue($checkRequired, $value, $valueToMatch)
    {
        if (!$checkRequired) {
            return true;
        }
        return ($value == $valueToMatch);
    }

    public function getFields($resource)
    {
        return $resource->schema();
    }

    public function getRequiredFields($resource)
    {
        return array_keys($this->filterFields($resource, 'required', true));
    }

    public function getUniqueFields($resource)
    {
        return array_keys($this->filterFields($resource, 'unique', true));
    }

    public function getFieldsWithDefaults($resource)
    {
        return $this->filterFields($resource, 'default', null, false);
    }
}
