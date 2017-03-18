<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Fathomminds\Rest\Exceptions\DetailedException;
use Fathomminds\Rest\Objects\RestObject as CoreRestObject;

class RestObject extends CoreRestObject
{
    protected $primaryKey = '_id';
    protected $databaseClass = Database::class;

    public function getUniqueFields()
    {
        return $this->schema->getUniqueFields();
    }

    public function validateUniqueFields()
    {
        $uniqueFields = $this->getUniqueFields();
        if (empty($uniqueFields)) {
            return;
        }
        $query = $this->getCollection();
        if (property_exists($this->resource, $this->primaryKey)) {
            $query->where($this->primaryKey, '!=', $this->getPrimaryKeyValue());
        }
        $query = $this->getUniqueFieldQuery($query, $uniqueFields);
        $res = $query->limit(1)->get();
        if ((int)$res->hits() > 0) {
            throw new DetailedException(
                'Unique constraint violation',
                ['resourceName' => $this->resourceName,'confilct' => $res[0]]
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
