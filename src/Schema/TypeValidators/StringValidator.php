<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\RestException;

class StringValidator extends StdTypeValidator
{
    protected $validType = 'string';
    private $maxLength = 20;

    public function __construct($params = [])
    {
        $this->maxLength = isset($params['maxLength']) ? $params['maxLength'] : 0;
    }

    public function validate($value)
    {
        $this->validateType($value);
        $this->validateLength($value);
    }

    public function validateLength($value)
    {
        if ($this->maxLength > 0 && strlen($value) > $this->maxLength) {
            throw new RestException(
                'Maximum length error',
                [
                    'length' => strlen($value),
                    'maximumLength' => $this->maxLength,
                ]
            );
        }
    }
}
