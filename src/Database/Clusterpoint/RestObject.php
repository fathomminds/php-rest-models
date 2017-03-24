<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Objects\RestObject as CoreRestObject;

class RestObject extends CoreRestObject
{
    protected $primaryKey = '_id';
    protected $databaseClass = Database::class;

    public function validateUniqueFields()
    {
        $uniqueFields = $this->getUniqueFields();
        $query = $this->getClient()->database($this->getDatabaseName() . '.' . $this->resourceName);
        if ($this->updateMode) {
            $uniqueFields = array_diff($uniqueFields, [$this->primaryKey]);
            $query->where($this->primaryKey, '!=', $this->getPrimaryKeyValue());
        }
        $query = $this->getUniqueFieldQuery($query, $uniqueFields);
        $res = $query->limit(1)->get();
        if ((int)$res->hits() > 0) {
            $results = json_decode($res->rawResponse())->results;
            $message = $results[0]->{$this->primaryKey} === $this->getPrimaryKeyValue() ?
                'Primary key collision' :
                'Unique constraint violation';
            throw new RestException(
                $message,
                ['resourceName' => $this->resourceName, 'confilct' => $results[0]]
            );
        }
    }

    private function getUniqueFieldQuery($query, $uniqueFields)
    {
        // @codeCoverageIgnoreStart
        $query->where(function ($query) use ($uniqueFields) {
            foreach ($uniqueFields as $fieldName) {
                if (property_exists($this->resource, $fieldName)) {
                    $query->orWhere($fieldName, '==', $this->resource->{$fieldName});
                }
            }
        });
        // @codeCoverageIgnoreEnd
        return $query;
    }
}
