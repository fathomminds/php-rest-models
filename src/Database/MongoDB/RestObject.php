<?php
namespace Fathomminds\Rest\Database\MongoDB;

use Fathomminds\Rest\Objects\RestObject as CoreRestObject;
use MongoDB\Client;

/**
 *
 * @method Client getClient()
 *
 */


class RestObject extends CoreRestObject
{
    protected $primaryKey = '_id';
    protected $databaseClass = Database::class;

    public function find($client = null)
    {
        if ($client === null) {
            $client = $this->getClient();
        }
        return (new Finder($client))
            ->database($this->getDatabaseName())
            ->from($this->getResourceName());
    }

    public function validateUniqueFields()
    {
        //
    }

    public function query()
    {
        $query = $this->getClient()->selectDatabase($this->getDatabaseName())->selectCollection($this->resourceName);
        return $query;
    }
}
