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
        $this->failOnUniqueFieldViolation($res, $uniqueFields);
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

    private function failOnUniqueFieldViolation($clusterpointResult, $uniqueFields)
    {
        if ($clusterpointResult->hits() === '0') { // Hits returned as string by CP
            return;
        }
        $doc = $clusterpointResult[0]; // limit 1 is applied in validateUniqueFields()
        $confilcts = [];
        foreach ($uniqueFields as $fieldName) {
            if (property_exists($this->resource, $fieldName) &&
                property_exists($doc, $fieldName) &&
                $this->resource->{$fieldName} === $doc->{$fieldName}
            ) {
                $confilcts[] = $fieldName;
            }
            throw new DetailedException(
                'Unique constraint violation',
                [
                    'resourceName' => $this->resourceName,
                    'confilcts' => $confilcts,
                ]
            );
        }
    }
}
