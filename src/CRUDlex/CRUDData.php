<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDEntity;

/**
 * The abstract class for reading and writing data.
 */
abstract class CRUDData {

    /**
     * Holds the {@see CRUDEntityDefinition} entity definition.
     */
    protected $definition;

    /**
     * Holds the {@see CRUDFileProcessorInterface} file processor.
     */
    protected $fileProcessor;

    /**
     * Creates an {@see CRUDEntity} from the raw data array with the field name
     * as keys and field values as values.
     *
     * @param array $row
     * the array with the raw data
     *
     * @return CRUDEntity
     * the entity containing the array data then
     */
    protected function hydrate(array $row) {
        $fieldNames = $this->definition->getFieldNames();
        $entity = new CRUDEntity($this->definition);
        foreach ($fieldNames as $fieldName) {
            $entity->set($fieldName, $row[$fieldName]);
        }
        return $entity;
    }

    /**
     * Gets the entity with the given id.
     *
     * @param string $id
     * the id
     *
     * @return CRUDEntity
     * the entity belonging to the id or null if not existant
     */
    public abstract function get($id);


    /**
     * Gets a list of entities fullfilling the given filter or all if no
     * selection was given.
     *
     * @param array $filter
     * the filter all resulting entities must fulfill, the keys as field names
     * @param array $filterOperators
     * the operators of the filter like "=" defining the full condition of the field
     * @param integer $skip
     * if given and not null, it specifies the amount of rows to skip
     * @param integer $amount
     * if given and not null, it specifies the maximum amount of rows to retrieve
     *
     * @return array
     * the entities fulfilling the filter or all if no filter was given
     */
    public abstract function listEntries(array $filter = array(), array $filterOperators = array(), $skip = null, $amount = null);

    /**
     * Persists the given entity as new entry in the datasource.
     *
     * @param CRUDEntity $entity
     * the entity to persist.
     */
    public abstract function create(CRUDEntity $entity);

    /**
     * Updates an existing entry in the datasource having the same id.
     *
     * @param CRUDEntity $entity
     * the entity with the new data
     */
    public abstract function update(CRUDEntity $entity);

    /**
     * Deletes an entry from the datasource having the given id.
     *
     * @param string $id
     * the id of the entry to delete
     *
     * @return boolean
     * true on successful deletion
     */
    public abstract function delete($id);

    /**
     * Gets ids and names of a table. Used for building up the dropdown box of
     * reference type fields.
     *
     * @param string $table
     * the table
     * @param string nameField
     * the field defining the name of the rows
     *
     * @return array
     * an array with the ids as key and the names as values
     */
    public abstract function getReferences($table, $nameField);

    /**
     * Retrieves the amount of entities in the datasource fulfilling the given
     * parameters.
     *
     * @param string $table
     * the table to count in
     * @param array $params
     * an array with the field names as keys and field values as values
     * @param array $paramOperators
     * the operators of the parameters like "=" defining the full condition of the field
     * @param bool $excludeDeleted
     * false, if soft deleted entries in the datasource should be counted, too
     *
     * @return int
     * the count fulfilling the given parameters
     */
    public abstract function countBy($table, array $params, array $paramsOperators, $excludeDeleted);

    /**
     * Adds the id and name of referenced entities to the given entities. Each
     * reference field is before the raw id of the referenced entity and after
     * the fetch, it's an array with the keys id and name.
     *
     * @param array $entities
     * the entities to fetch the references for
     */
    public abstract function fetchReferences(array &$entities = null);

    /**
     * Gets the {@see CRUDEntityDefinition} instance.
     *
     * @return CRUDEntityDefinition
     * the definition instance
     */
    public function getDefinition() {
        return $this->definition;
    }

    /**
     * Creates a new, empty entity instance having all fields prefilled with
     * null or the defined value in case of fixed fields.
     *
     * @return CRUDEntity
     * the newly created entity
     */
    public function createEmpty() {
        $entity = new CRUDEntity($this->definition);
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            $value = null;
            if ($this->definition->getType($field) == 'fixed') {
                $value = $this->definition->getFixedValue($field);
            }
            $entity->set($field, $value);
        }
        $entity->set('id', null);
        return $entity;
    }

    /**
     * Creates the uploaded files of a newly created entity.
     *
     * @param Request $request
     * the HTTP request containing the file data
     * @param CRUDEntity $entity
     * the just created entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     */
    public function createFiles(Request $request, CRUDEntity $entity, $entityName) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $this->fileProcessor->createFile($request, $entity, $entityName, $field);
            }
        }
    }

    /**
     * Updates the uploaded files of an updated entity.
     *
     * @param Request $request
     * the HTTP request containing the file data
     * @param CRUDEntity $entity
     * the updated entity
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     */
    public function updateFiles(Request $request, CRUDEntity $entity, $entityName) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $this->fileProcessor->updateFile($request, $entity, $entityName, $field);
            }
        }
    }

    /**
     * Deletes a specific file from an existing entity.
     *
     * @param CRUDEntity $entity
     * the entity to delete the file from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the field of the entity containing the file to be deleted
     */
    public function deleteFile(CRUDEntity $entity, $entityName, $field) {
        $this->fileProcessor->deleteFile($entity, $entityName, $field);
    }

    /**
     * Deletes all files of an existing entity.
     *
     * @param CRUDEntity $entity
     * the entity to delete the files from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     */
    public function deleteFiles(CRUDEntity $entity, $entityName) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $this->fileProcessor->deleteFile($entity, $entityName, $field);
            }
        }
    }

    /**
     * Renders (outputs) a file of an entity. This includes setting headers
     * like the file size, mimetype and name, too.
     *
     * @param CRUDEntity $entity
     * the entity to render the file from
     * @param string $entityName
     * the name of the entity as this class here is not aware of it
     * @param string $field
     * the field of the entity containing the file to be rendered
     *
     * @return Response
     * the HTTP response, likely to be a streamed one
     */
    public function renderFile(CRUDEntity $entity, $entityName, $field) {
        return $this->fileProcessor->renderFile($entity, $entityName, $field);
    }

}
