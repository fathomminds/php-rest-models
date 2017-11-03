<?php
namespace Fathomminds\Rest\Helpers;

use Fathomminds\Rest\Exceptions\RestException;

abstract class ObjectMapper
{
    public static function map($object, $map)
    {
        if (gettype($object) !== 'object') {
            throw new RestException('ObjectMapper map method expects object as first parameter', [
                'parameter' => $object,
            ]);
        }
        if (!is_array($map)) {
            throw new RestException('ObjectMapper map method expects array as second parameter', [
                'parameter' => $map,
            ]);
        }
        $mappedObject = new \StdClass();
        foreach ($map as $targetFieldName => $sourceFieldName) {
            list($propertyExists, $propertyValue) = static::getPropertyValue($object, $sourceFieldName);
            if ($propertyExists) {
                $mappedObject = static::setPropertyValue(
                    $mappedObject,
                    $targetFieldName,
                    $propertyValue
                );
            }
        }
        return $mappedObject;
    }

    private static function setPropertyValue($mappedObject, $targetFieldName, $propertyValue)
    {
        $fieldNameArr = explode('.', $targetFieldName);
        $fieldName = array_shift($fieldNameArr);
        if (count($fieldNameArr) !== 0) {
            if (!property_exists($mappedObject, $fieldName)) {
                $mappedObject->{$fieldName} = new \StdClass();
            }
            $mappedObject->{$fieldName} = static::setPropertyValue(
                $mappedObject->{$fieldName},
                implode('.', $fieldNameArr),
                $propertyValue
            );
            return $mappedObject;
        }
        $mappedObject->{$fieldName} = $propertyValue;
        return $mappedObject;
    }

    private static function getPropertyValue($object, $sourceFieldName)
    {
        $fieldNameArr = explode('.', $sourceFieldName);
        $fieldName = array_shift($fieldNameArr);
        if (!property_exists($object, $fieldName)) {
            return [
                false,
                null
            ];
        }
        if (count($fieldNameArr) === 0) {
            return [
                true,
                json_decode(json_encode($object->{$fieldName}))
            ];
        }
        return static::getPropertyValue($object->{$fieldName}, implode('.', $fieldNameArr));
    }
}
