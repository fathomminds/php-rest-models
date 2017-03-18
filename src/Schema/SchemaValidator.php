<?php
namespace Fathomminds\Rest\Schema;

use Fathomminds\Rest\Contracts\ISchema;
use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Exceptions\DetailedException;

class SchemaValidator implements ISchema
{
    protected $fields = [];

    public function validate($resource)
    {
        $this->expectObject($resource);
        $errors = array_merge(
            $this->validateRequiredFields($resource),
            $this->validateExtraneousFields($resource),
            $this->validateFieldTypes($resource)
        );
        if (!empty($errors)) {
            throw new DetailedException(
                'Invalid structure',
                [
                    'schema' => get_called_class(),
                    'errors' => $errors,
                ]
            );
        }
    }

    private function expectObject($resource)
    {
        if (gettype($resource) !== 'object') {
            throw new DetailedException(
                'Object expected',
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
        $requiredFields = $this->getRequiredFields();
        foreach ($requiredFields as $fieldName) {
            if (!property_exists($resource, $fieldName)) {
                $errors[$fieldName] = 'Missing required field';
            }
        }
        return $errors;
    }

    private function validateExtraneousFields($resource)
    {
        $errors = [];
        foreach (array_keys(get_object_vars($resource)) as $fieldName) {
            if (!isset($this->fields[$fieldName])) {
                $errors[$fieldName] = 'Extraneous field';
            }
        }
        return $errors;
    }

    private function validateFieldTypes($resource)
    {
        $validatorFactory = new ValidatorFactory;
        $errors = [];
        foreach ($this->fields as $fieldName => $rules) {
            if (property_exists($resource, $fieldName)) {
                try {
                    $validatorFactory->create($rules)->validate($resource->{$fieldName});
                } catch (DetailedException $ex) {
                    $errors[$fieldName]['error'] = $ex->getMessage();
                    $errors[$fieldName]['details'] = $ex->getDetails();
                }
            }
        }
        return $errors;
    }

    private function filterFields($paramKey, $paramValue)
    {
        $fields = [];
        foreach ($this->fields as $fieldName => $params) {
            if (isset($params[$paramKey]) && $params[$paramKey] == $paramValue) {
                $fields[] = $fieldName;
            }
        }
        return $fields;
    }

    public function getRequiredFields()
    {
        return $this->filterFields('required', true);
    }

    public function getUniqueFields()
    {
        return $this->filterFields('unique', true);
    }
}
