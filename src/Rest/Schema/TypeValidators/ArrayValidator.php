<?php
namespace Fathomminds\Clurexid\Rest\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

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
        $this->keyValidator = (new ValidatorFactory())->create($this->keyRules);
        $this->itemRules = isset($params['item']) ? $params['item'] : null;
        $this->itemValidator = (new ValidatorFactory())->create($this->itemRules);
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
            throw new DetailedException(
                'Array validation failed',
                $details
            );
        }
    }

    private function validateKeys($value)
    {
        $errors = [];
        foreach (array_keys($value) as $key) {
            try {
                $this->keyValidator->validate($key);
            } catch (DetailedException $ex) {
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
        foreach ($value as $key => $item) {
            try {
                $this->itemValidator->validate($item);
            } catch (DetailedException $ex) {
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
