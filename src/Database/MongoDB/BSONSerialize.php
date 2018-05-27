<?php
namespace Fathomminds\Rest\Database\MongoDB;

use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Fathomminds\Rest\Schema\TypeValidators\MongoIdValidator;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;


class BSONSerialize
{
    public function serializeObject($res) {
        if (empty($res)) {
            return null;
        }
        if (!is_object($res)) {
            return $res;
        }
        $className = get_class($res);
        print($className . "\n");
        if ($className === ObjectId::class) {
            return (string)$res;
        }
        if ($className === BSONDocument::class) {
            return $this->bsonDocumentSerialize($res);
        }
        if ($className === BSONArray::class) {
            return $this->bsonArraySerialize($res);
        }
        return $res;
    }

    private function bsonDocumentSerialize($document) {
        $res = $document->bsonSerialize();
        $resProps = ($res);
        foreach ($resProps as $propName => $propValue) {
            $res->$propName = $this->serializeObject($propValue);
        }
        return $res;
    }

    private function bsonArraySerialize($array) {
        $res = $array->bsonSerialize();
        foreach ($res as $key => $item) {
            $res[$key] = $this->serializeObject($item);
        }
        return $res;
    }
}