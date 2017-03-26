<?php
namespace Fathomminds\Rest\Schema;

use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Exceptions\RestException;

class SchemaValidator
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
            throw new RestException(
                'Invalid structure',
                [
                    'schema' => get_class($resource),
                    'errors' => $errors,
                ]
            );
        }
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

    private function validateRequiredFields($resource)
    {
        $errors = [];
        $requiredFields = $this->getRequiredFields($resource);
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
            if (!isset($resource->schema()[$fieldName])) {
                $errors[$fieldName] = 'Extraneous field';
            }
        }
        return $errors;
    }

    private function validateFieldTypes($resource)
    {
        $validatorFactory = new ValidatorFactory;
        $errors = [];
        foreach ($resource->schema() as $fieldName => $rules) {
            if (property_exists($resource, $fieldName)) {
                try {
                    $validatorFactory->create($rules)->validate($resource->{$fieldName});
                } catch (RestException $ex) {
                    $errors[$fieldName]['error'] = $ex->getMessage();
                    $errors[$fieldName]['details'] = $ex->getDetails();
                }
            }
        }
        return $errors;
    }

    private function filterFields($resource, $paramKey, $paramValue)
    {
        $fields = [];
        foreach ($resource->schema() as $fieldName => $params) {
            if (isset($params[$paramKey]) && $params[$paramKey] == $paramValue) {
                $fields[] = $fieldName;
            }
        }
        return $fields;
    }

    public function getFields($resource)
    {
        return $resource->schema();
    }

    public function getRequiredFields($resource)
    {
        return $this->filterFields($resource, 'required', true);
    }

    public function getUniqueFields($resource)
    {
        return $this->filterFields($resource, 'unique', true);
    }
}
