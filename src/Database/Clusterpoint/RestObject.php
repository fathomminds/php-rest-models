<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Objects\RestObject as CoreRestObject;

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
        $query = $this->getUniqueFieldQuery();
        $res = $query->get();
        if ((int)$res->hits() > 0) {
            $results = json_decode($res->rawResponse())->results;
            $message = $results[0]->{$this->primaryKey} === $this->getPrimaryKeyValue() ?
                'Primary key collision' : 'Unique constraint violation';
            throw new RestException(
                $message,
                ['resourceName' => $this->resourceName, 'confilct' => $results[0]]
            );
        }
    }


    private function getUniqueFieldQuery()
    {
        $uniqueFields = $this->getUniqueFields();
        $query = $this->getClient()->database($this->getDatabaseName() . '.' . $this->resourceName);
        if ($this->isModification()) {
            $uniqueFields = array_diff($uniqueFields, [$this->primaryKey]);
            $query->where($this->primaryKey, '!=', $this->getPrimaryKeyValue());
        }
        // @codeCoverageIgnoreStart
        $query->where(function($query) use ($uniqueFields) {
            foreach ($uniqueFields as $fieldName) {
                if (property_exists($this->resource, $fieldName)) {
                    $query->orWhere($fieldName, '==', $this->resource->{$fieldName});
                }
            }
        });
        // @codeCoverageIgnoreEnd
        return $query->limit(1);
    }

    private function isModification()
    {
        if ($this->updateMode() || $this->replaceMode()) {
            return true;
        }
        return false;
    }

    public function query()
    {
        $query = $this->getClient()->database($this->getDatabaseName() . '.' . $this->resourceName);
        return $query;
    }
}
