<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Schema\SchemaValidator;

class ValidatorFactory
{
    public function create($rules, $updateMode, $replaceMode)
    {
        $validatorClass = $this->getValidatorClass($rules);
        $params = $this->getValidatorParameters($rules);
        if (isset($rules['type']) && $rules['type'] === 'schema') {
            $validator = new SchemaValidator($rules['validator']['class']);
            $validator->updateMode($updateMode);
            $validator->replaceMode($replaceMode);
            return $validator;
        }
        $validator = $validatorClass->newInstanceArgs(empty($params) ? [] : [$params]);
        $validator->updateMode($updateMode);
        $validator->replaceMode($replaceMode);
        return $validator;
    }

    private function getValidatorClass($rules)
    {
        $validatorClassName = $rules['validator']['class'];
        try {
            $validatorClass = new \ReflectionClass($validatorClassName);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage());
        }
        return $validatorClass;
    }

    private function getValidatorParameters($rules)
    {
        $parameters = [];
        if (isset($rules['validator']) && isset($rules['validator']['params'])) {
            $parameters = $rules['validator']['params'];
        }
        return $parameters;
    }
}

