<?php
namespace Fathomminds\Rest\Database\MongoDB;

use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Fathomminds\Rest\Schema\TypeValidators\MongoIdValidator;
use MongoDB\BSON\ObjectId;


class MongoIdCast
{
    private const TYPE_SCHEMA = "TYPE_SCHEMA";
    private const TYPE_ARRAY = "TYPE_ARRAY";
    private const TYPE_MONGOID = "TYPE_MONGOID";
    private const TYPE_OTHER = "TYPE_OTHER";

    private const WAY_NOT_SET = "WAY_NOT_SET";
    private const WAY_TO_MONGO_ID = "WAY_TO_MONGO_ID";
    private const WAY_TO_STRING = "WAY_TO_STRING";

    private $way;

    public function __construct()
    {
        $this->resetWay();
    }

    private function resetWay()
    {
        $this->way = self::WAY_NOT_SET;
    }

    private function toMongoId()
    {
        $this->way = self::WAY_TO_MONGO_ID;
    }

    private function toString()
    {
        $this->way = self::WAY_TO_STRING;
    }

    public function castSingleValueToMongoId($propertyName, $propertyValue, $schema)
    {
        $propertyType = $this->getPropertyType($propertyName, $schema);
        if ($propertyType === self::TYPE_MONGOID) {
            return new ObjectId($propertyValue);
        }
        return $propertyValue;
    }

    public function castToMongoId($object, $schema)
    {
        $this->toMongoId();
        $result = $this->cast($object, $schema);
        $this->resetWay();
        return $result;
    }

    public function castToString($object, $schema)
    {
        $this->toString();
        $result = $this->cast($object, $schema);
        $this->resetWay();
        return $result;
    }

    private function cast($object, $schema)
    {
        if (!is_object($object)) {
            return $object;
        }
        return $this->castSchema($object, $schema);
    }

    private function castSchema($object, $schema)
    {
        $properties = get_object_vars($object);
        foreach ($properties as $propertyName => $propertyValue) {
            $propertyType = $this->getPropertyType($propertyName, $schema);
            switch ($propertyType) {
                case self::TYPE_MONGOID:
                    $object->{$propertyName} = $this->castMongoId($propertyValue);
                    break;
                case self::TYPE_SCHEMA:
                    $schemaName = $schema[$propertyName]['validator']['class'];
                    $object->{$propertyName} = $this->castSchema(
                        $propertyValue,
                        (new $schemaName())->schema()
                    );
                    break;
                case self::TYPE_ARRAY:
                    $itemDetails = $schema[$propertyName]['validator']['params']['item'];
                    $object->{$propertyName} = $this->castArray(
                        $propertyValue,
                        $itemDetails
                    );
                    break;
                default:
                    $object->{$propertyName} = $propertyValue;
            }
        }
        return $object;
    }

    private function getPropertyType($propertyName, $schema)
    {
        if (array_key_exists('type', $schema[$propertyName]) && $schema[$propertyName]['type'] === 'schema') {
            return self::TYPE_SCHEMA;
        }
        if ($schema[$propertyName]['validator']['class'] === ArrayValidator::class) {
            return self::TYPE_ARRAY;
        }
        if ($schema[$propertyName]['validator']['class'] === MongoIdValidator::class) {
            return self::TYPE_MONGOID;
        }
        return self::TYPE_OTHER;
    }

    private function getArrayItemType($itemDetails)
    {
        if (array_key_exists('type', $itemDetails) && $itemDetails['type'] === 'schema') {
            return self::TYPE_SCHEMA;
        }
        if ($itemDetails['validator']['class'] === ArrayValidator::class) {
            return self::TYPE_ARRAY;
        }
        if ($itemDetails['validator']['class'] === MongoIdValidator::class) {
            return self::TYPE_MONGOID;
        }
        return self::TYPE_OTHER;
    }

    private function castMongoId($value)
    {
        if ($this->way === self::WAY_TO_STRING) {
            return (string)$value;
        }
        if ($this->way === self::WAY_TO_MONGO_ID) {
            return new ObjectId($value);
        }
        return $value;
    }

    private function castArray($array, $itemDetails)
    {
        $itemType = $this->getArrayItemType($itemDetails);
        if ($itemType === self::TYPE_OTHER) {
            return $array;
        }
        foreach ($array as $key => $item) {
            switch ($itemType) {
                case self::TYPE_MONGOID:
                    $array[$key] = $this->castMongoId($item);
                    break;
                case self::TYPE_SCHEMA:
                    $schemaName = $itemDetails['validator']['class'];
                    $array[$key] = $this->castSchema(
                        $item,
                        (new $schemaName())->schema()
                    );
                    break;
                case self::TYPE_ARRAY:
                    $nestedItemDetails = $itemDetails['validator']['params']['item'];
                    $array[$key] = $this->castArray(
                        $item,
                        $nestedItemDetails
                    );
                    break;
                default:
                    $array[$key] = $item;
            }
        }
        return $array;
    }
}