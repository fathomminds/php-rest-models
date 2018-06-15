<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use MongoDB\BSON\ObjectId;
use Fathomminds\Rest\Exceptions\RestException;

class MongoIdValidator extends StringValidator
{
    public function validate($value)
    {
        parent::validate($value);
        try {
            new ObjectId($value);
        } catch (\Exception $ex) {
            throw new RestException(
                'Invalid MongoDB Object ID',
                [
                    'value' => $value,
                ]
            );
        }
    }
}
