<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\DetailedException;

class ValidatorFactory
{
    public function create($rules)
    {
        $validatorClass = $this->getValidatorClass($rules);
        $params = $this->getValidatorParameters($rules);
        return $validatorClass->newInstanceArgs(empty($params) ? [] : [$params]);
    }

    private function getValidatorClass($rules)
    {
        $validatorClassName = $rules['validator']['class'];
        try {
            $validatorClass = new \ReflectionClass($validatorClassName);
        } catch (\Exception $ex) {
            throw new DetailedException($ex->getMessage());
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
