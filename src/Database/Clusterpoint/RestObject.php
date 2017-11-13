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
            $query->where($this->primaryKey, '!=', $this->getPrimaryKeyValue(), false);
        }
        // @codeCoverageIgnoreStart
        $query->where(function($query) use ($uniqueFields) {
            foreach ($uniqueFields as $fieldName) {
                list($propertyExists, $propertyValue) = $this->getProperty($fieldName);
                if ($propertyExists) {
                    $this->validateUniqueFieldDataType($fieldName, $propertyValue);
                    $query->orWhere($fieldName, '==', $propertyValue, false);
                }
            }
        });
        // @codeCoverageIgnoreEnd
        return $query->limit(1);
    }

    private function getProperty($fieldNameDotted, $resource = null)
    {
        if ($resource === null) {
            $resource = $this->resource;
        }
        $fieldNameArr = explode('.', $fieldNameDotted);
        $fieldName = array_shift($fieldNameArr);
        if (!property_exists($resource, $fieldName)) {
            return [
                false,
                null
            ];
        }
        if (count($fieldNameArr) === 0) {
            return [
                true,
                $resource->{$fieldName}
            ];
        }
        return $this->getProperty(implode('.', $fieldNameArr), $resource->{$fieldName});
    }

    private function validateUniqueFieldDataType($fieldName, $propertyValue)
    {
        $dataType = gettype($propertyValue);
        if (in_array($dataType, $this->validUniqueFieldTypes)) {
            return;
        }
        throw new RestException(
            'Data type is invalid for unique field',
            [
                'resourceName' => $this->resourceName,
                'fieldName' => $fieldName,
                'data' => $propertyValue,
                'dataType' => $dataType,
                'validDataTypes' => $this->validUniqueFieldTypes
            ]
        );
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
