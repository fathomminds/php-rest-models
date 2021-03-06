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
        parent::__construct($params);
        $this->keyRules = isset($params['key']) ? $params['key'] : null;
        $this->keyValidator = (new ValidatorFactory())->create($this->keyRules, false, false);
        $this->itemRules = isset($params['item']) ? $params['item'] : null;
        $this->itemValidator = (new ValidatorFactory())->create($this->itemRules, false, false);
    }

    public function validate($value)
    {
        $details = [];
        $this->validateType($value);
        if ($value !== null) {
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
    }

    private function validateKeys($value)
    {
        $errors = [];
        $this->keyValidator->updateMode($this->updateMode());
        $this->keyValidator->replaceMode($this->replaceMode());
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
        $this->itemValidator->replaceMode($this->replaceMode());
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

    public static function cast($value, $params = null, $skipExtraneous = false)
    {
        if (!is_array($value) || !self::isValidCastParams($params)) {
            return $value;
        }
        $result = [];
        foreach ($value as $key => $value) {
            $arrayKey = static::castKey($key, $params, $skipExtraneous);
            $arrayValue = static::castItem($value, $params, $skipExtraneous);
            $result[$arrayKey] = $arrayValue;
        }
        return $result;
    }

    private static function castKey($key, $params, $skipExtraneous)
    {
        return static::castProperty('key', $key, $params, $skipExtraneous);
    }

    private static function castItem($value, $params, $skipExtraneous)
    {
        return static::castProperty('item', $value, $params, $skipExtraneous);
    }

    private static function castProperty($propertyType, $propertyValue, $params, $skipExtraneous)
    {
        if (isset($params[$propertyType]['type']) && $params[$propertyType]['type'] === 'schema') {
            return $params[$propertyType]['validator']['class']::cast($propertyValue, $skipExtraneous);
        }
        return $params[$propertyType]['validator']['class']::cast($propertyValue, $params, $skipExtraneous);
    }

    private static function isValidCastParams($params)
    {
        if (!is_array($params)) {
            return false;
        }
        if (empty($params['key']['validator'])) {
            return false;
        }
        if (empty($params['item']['validator'])) {
            return false;
        }
        return true;
    }
}
