<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\RestException;

class NumberTypeValidator extends StdTypeValidator
{
    private $min;
    private $max;

    public function __construct($params = [])
    {
        $this->min = isset($params['min']) ? $params['min'] : null;
        $this->max = isset($params['max']) ? $params['max'] : null;
    }

    public function validate($value)
    {
        $this->validateType($value);
        $this->validateMin($value);
        $this->validateMax($value);
    }

    private function validateMin($value)
    {
        if ($this->min !== null && $value < $this->min) {
            throw new RestException(
                'Minimum value error',
                [
                    'value' => $value,
                    'min' => $this->min,
                ]
            );
        }
    }

    private function validateMax($value)
    {
        if ($this->max !== null && $value > $this->max) {
            throw new RestException(
                'Maximum value error',
                [
                    'value' => $value,
                    'min' => $this->max,
                ]
            );
        }
    }
}
