<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Objects\RestObject as CoreRestObject;

class RestObject extends CoreRestObject
{
    protected $primaryKey = 'id';
    protected $databaseClass = Database::class;

    public function validateUniqueFields()
    {
        $uniqueFields = $this->getUniqueFields();
        if (empty($uniqueFields)) {
            return;
        }
        $client = $this->getClient();
        var_dump($client);
        return;
        if ((int)$res->hits() > 0) {
            throw new RestException(
                'Unique constraint violation',
                ['resourceName' => $this->resourceName, 'confilct' => $res[0]]
            );
        }
    }
}
