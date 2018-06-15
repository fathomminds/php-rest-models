<?php
namespace Fathomminds\Rest\Database\MongoDB;


use MongoDB\BSON\ObjectId;

class MongoUpdateSerialize
{
    public function serialize($value)
    {
        if (!is_object($value)) {
            return $value;
        }
        return (object)$this->processObject($value, "");
    }

    private function processObject($object, $prefix)
    {
        $res = [];
        $props = get_object_vars($object);
        foreach ($props as $propName => $propValue) {
            $res = array_merge($res, $this->processField($prefix, $propName, $propValue));
        }
        return $res;
    }

    private function processArray($array, $prefix)
    {
        $res = [];
        foreach ($array as $itemIdx => $itemValue) {
            $res = array_merge($res, $this->processField($prefix, $itemIdx, $itemValue));
        }
        return $res;
    }

    private function processField($prefix, $fieldName, $fieldValue)
    {
        if (is_object($fieldValue)) {
            if (get_class($fieldValue) === ObjectId::class) {
                return[
                    $this->getPrefix($prefix, $fieldName) => $fieldValue
                ];
            }
            return $this->processObject($fieldValue, $this->getPrefix($prefix, $fieldName));
        }
        if (is_array($fieldValue)) {
            return $this->processArray($fieldValue, $this->getPrefix($prefix, $fieldName));
        }
        return [
            $this->getPrefix($prefix, $fieldName) => $fieldValue
        ];
    }

    private function getPrefix($prefix, $propName)
    {
        if (empty($prefix)) {
            return $propName;
        }
        return $prefix . "." . $propName;
    }
}