<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\RestException;

class ArrayValidator extends StdTypeValidator
{
    protected $validType = 'array';
    private $keyRules;
    private $keyValidator;
    private $itemRules;
    private $itemValidator;

    public function __construct($params = [])
    {
        $this->keyRules = isset($params['key']) ? $params['key'] : null;
        $this->keyValidator = (new ValidatorFactory())->create($this->keyRules, false);
        $this->itemRules = isset($params['item']) ? $params['item'] : null;
        $this->itemValidator = (new ValidatorFactory())->create($this->itemRules, false);
    }

    public function validate($value)
    {
        $details = [];
        $this->validateType($value);
        $details['keyErrors'] = $this->validateKeys($value);
        if (empty($details['keyErrors'])) {
            unset($details['keyErrors']);
        }
        $details['itemErrors'] = $this->validateItems($value);
        if (empty($details['itemErrors'])) {
            unset($details['itemErrors']);
        }
        if (!empty($details)) {
            throw new RestException(
                'Array validation failed',
                $details
            );
        }
    }

    private function validateKeys($value)
    {
        $errors = [];
        $this->keyValidator->updateMode($this->updateMode());
        foreach (array_keys($value) as $key) {
            try {
                $this->keyValidator->validate($key);
            } catch (RestException $ex) {
                $errors[] = [
                    'validation' => 'key',
                    'key' => $key,
                    'error' => $ex->getMessage(),
                ];
            }
        }
        return $errors;
    }

    private function validateItems($value)
    {
        $errors = [];
        $this->itemValidator->updateMode($this->updateMode());
        foreach ($value as $key => $item) {
            try {
                $this->itemValidator->validate($item);
            } catch (RestException $ex) {
                $errorItem = [];
                $errorItem['validation'] = 'item';
                $errorItem['key'] = $key;
                $errorItem['error'] = $ex->getMessage();
                if (!empty($ex->getDetails())) {
                    $errorItem['details'] = $ex->getDetails();
                }
                $errors[] = $errorItem;
            }
        }
        return $errors;
    }
}
