<?php
namespace Fathomminds\Clurexid\Rest\Database\Clusterpoint;

use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;
use Fathomminds\Clurexid\Rest\Objects\RestObject as CoreRestObject;

class RestObject extends CoreRestObject
{
    protected $primaryKey = '_id';
    protected $databaseClass = Database::class;

    public function validateUniqueFields()
    {
        $uniqueFields = $this->schema->getUniqueFields();
        if (empty($uniqueFields)) {
            return;
        }
        $query = null;
        if (property_exists($this->resource, $this->primaryKey)) {
            $query = $this->getCollection();
            $query->where($this->primaryKey, '!=', $this->getPrimaryKeyValue());
        }
        $query = $this->getUniqueFieldQuery($query, $uniqueFields);
        $res = $query->limit(1)->get();
        if ((int)$res->hits() > 0) {
            throw new DetailedException(
                'Unique constraint violation',
                [
                    'resourceName' => $this->resourceName,
                    'confilct' => $res[0],
                ]
            );
        }
    }

    private function getUniqueFieldQuery($query = null, $uniqueFields = null)
    {
        $uniqueFields = $uniqueFields === null ? $this->schema->getUniqueFields() : $uniqueFields;
        $query = $query === null ? $this->getCollection() : $query;
        $query->where(function ($query) use ($uniqueFields) {
            foreach ($uniqueFields as $fieldName) {
                if (property_exists($this->resource, $fieldName)) {
                    $query->orWhere($fieldName, '==', $this->resource->{$fieldName});
                }
            }
        });
        return $query;
    }
}
