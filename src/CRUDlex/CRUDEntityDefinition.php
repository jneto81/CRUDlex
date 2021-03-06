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

class CRUDEntityDefinition {

    /**
     * The table where the data is stored.
     */
    protected $table;

    /**
     * Holds all fields in the same structure as in the CRUD YAML file.
     */
    protected $fields;

    /**
     * The label for the entity.
     */
    protected $label;

    /**
     * An array with the children referencing the entity. All entries are
     * arrays with three referencing elements: table, fieldName, entity
     */
    protected $children;

    /**
     * Labels for the fields "id", "created_at" and "updated_at".
     */
    protected $standardFieldLabels;

    /**
     * An array containing the fields which should appear in the list view
     * of the entity.
     */
    protected $listFields;

    /**
     * The fields used to display the children on the details page of an entity.
     * The keys are the entity names as in the CRUD YAML and the values are the
     * field names.
     */
    protected $childrenLabelFields;

    /**
     * Whether to delete its children when an instance is deleted.
     */
    protected $deleteCascade;

    /**
     * The amount of items to display per page on the listview.
     */
    protected $pageSize;

    /**
     * The fields offering to be filtered.
     */
    protected $filter;

    /**
     * Holds the {@see CRUDServiceProvider}.
     */
    protected $serviceProvider;

    /**
     * Gets the field names exluding the given ones.
     *
     * @param array $exclude
     * the field names to exclude
     *
     * @return array
     * all field names excluding the given ones
     */
    protected function getFilteredFieldNames(array $exclude) {
        $fieldNames = $this->getFieldNames();
        $result = array();
        foreach ($fieldNames as $fieldName) {
            if (!in_array($fieldName, $exclude)) {
                $result[] = $fieldName;
            }
        }
        return $result;
    }

    /**
     * Gets the value of a field key.
     *
     * @param string $name
     * the name of the field
     * @param string $key
     * the value of the key
     *
     * @return mixed
     * the value of the field key or null if not existing
     */
    protected function getFieldValue($name, $key) {
        if (key_exists($name, $this->fields) && key_exists($key, $this->fields[$name])) {
            return $this->fields[$name][$key];
        }
        return null;
    }

    /**
     * Sets the value of a field key. If the field or the key in the field
     * don't exist, they get created.
     *
     * @param string $name
     * the name of the field
     * @param string $key
     * the value of the key
     * @param mixed $value
     * the new value
     */
    protected function setFieldValue($name, $key, $value) {
        if (!key_exists($name, $this->fields)) {
            $this->fields[$name] = array();
        }
        $this->fields[$name][$key] = $value;
    }

    /**
     * Gets the value of a reference field.
     *
     * @param string $fieldName
     * the field name of the reference
     * @param string $key
     * the key of the reference value
     *
     * @return string
     * the value of the reference field
     */
    protected function getReferenceValue($fieldName, $key) {
        if ($this->getType($fieldName) != 'reference') {
            return null;
        }
        return $this->fields[$fieldName]['reference'][$key];
    }

    /**
     * Constructor.
     *
     * @param string $table
     * the table of the entity
     * @param array $fields
     * the fieldstructure just like the CRUD YAML
     * @param string $label
     * the label of the entity
     * @param array $standardFieldLabels
     * labels for the fields "id", "created_at" and "updated_at"
     * @param CRUDServiceProvider $serviceProvider
     * The current service provider
     */
    public function __construct($table, array $fields, $label, array $standardFieldLabels, CRUDServiceProvider $serviceProvider) {
        $this->table = $table;
        $this->fields = $fields;
        $this->label = $label;
        $this->standardFieldLabels = $standardFieldLabels;
        $this->serviceProvider = $serviceProvider;

        $this->children = array();
        $this->listFields = array();
        $this->childrenLabelFields = array();
        $this->filter = array();
        $this->deleteCascade = false;
        $this->pageSize = 25;
    }

    /**
     * Gets all field names, including the implicit ones like "id" or
     * "created_at".
     *
     * @return array
     * the field names
     */
    public function getFieldNames() {
        $fieldNames = $this->getReadOnlyFields();
        foreach ($this->fields as $field => $value) {
            $fieldNames[] = $field;
        }
        return $fieldNames;
    }

    /**
     * Sets the field names to be used in the listview.
     *
     * @param array $listFields
     * the field names to be used in the listview
     */
    public function setListFieldNames(array $listFields) {
        $this->listFields = $listFields;
    }

    /**
     * Gets the field names to be used in the listview. If they were not specified,
     * all public field names are returned.
     *
     * @return array
     * the field names to be used in the listview
     */
    public function getListFieldNames() {
        if ($this->listFields) {
            return $this->listFields;
        }
        return $this->getPublicFieldNames();
    }

    /**
     * Gets the fields used to display the children on the details page of an
     * entity. The keys are the entity names as in the CRUD YAML and the values
     * are the field names.
     *
     * @return array
     * the fields used to display the children on the details page
     */
    public function getChildrenLabelFields() {
        return $this->childrenLabelFields;
    }

    /**
     * Sets the fields used to display the children on the details page of an
     * entity. The keys are the entity names as in the CRUD YAML and the values
     * are the field names.
     *
     * @param array $childrenLabelFields
     * the fields used to display the children on the details page
     */
    public function setChildrenLabelFields(array $childrenLabelFields) {
        $this->childrenLabelFields = $childrenLabelFields;
    }

    /**
     * Gets whether to delete its children when an instance is deleted.
     *
     * @return boolean
     * true if so
     */
    public function isDeleteCascade() {
        return $this->deleteCascade;
    }

    /**
     * Sets whether to delete its children when an instance is deleted.
     *
     * @param boolean $deleteCascade
     * whether to delete its children when an instance is deleted
     */
    public function setDeleteCascade($deleteCascade) {
        $this->deleteCascade = $deleteCascade;
    }

    /**
     * Gets the amount of items to display per page on the listview.
     *
     * @return integer
     * the amount of items to display per page on the listview
     */
    public function getPageSize() {
        return $this->pageSize;
    }


    /**
    * Sets the amount of items to display per page on the listview.
    *
    * @param integer $pageSize
    * the amount of items to display per page on the listview
    */
    public function setPageSize($pageSize) {
        $this->pageSize = $pageSize;
    }

    /**
     * Gets the fields offering a filter.
     *
     * @return array
     * the fields to filter
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * Sets the fields offering a filter.
     *
     * @param array $filter
     * the fields to filter
     */
    public function setFilter(array $filter) {
        $this->filter = $filter;
    }

    /**
     * Gets the service provider.
     *
     * @return CRUDServiceProvider
     * the service provider
     */
    public function getServiceProvider() {
        return $this->serviceProvider;
    }

    /**
     * Gets the public field names. The internal fields "version" and
     * "deleted_at" are filtered.
     *
     * @return array
     * the public field names
     */
    public function getPublicFieldNames() {
        $exclude = array('version', 'deleted_at');
        $result = $this->getFilteredFieldNames($exclude);
        return $result;
    }

    /**
     * Gets the field names which are editable. Not editable are fields like the
     * id or the created_at.
     *
     * @return array
     * the editable field names
     */
    public function getEditableFieldNames() {
        $result = $this->getFilteredFieldNames($this->getReadOnlyFields());
        return $result;
    }

    /**
     * Gets the read only field names like the id or the created_at.
     *
     * @return array
     * the read only field names
     */
    public function getReadOnlyFields() {
        return array('id', 'created_at', 'updated_at', 'version', 'deleted_at');
    }

    /**
     * Gets the type of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the type or null on invalid field name
     */
    public function getType($fieldName) {
        return $this->getFieldValue($fieldName, 'type');
    }

    /**
     * Sets the type of a field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new field type
     */
    public function setType($fieldName, $value) {
        return $this->setFieldValue($fieldName, 'type', $value);
    }

    /**
     * Gets whether a field is required.
     *
     * @param string $fieldName
     * the field name
     *
     * @return bool
     * true if so
     */
    public function isRequired($fieldName) {
        $result = $this->getFieldValue($fieldName, 'required');
        if ($result === null) {
            $result = false;
        }
        return $result;
    }


    /**
     * Sets whether a field is required.
     *
     * @param string $fieldName
     * the field name
     * @param bool $fieldName
     * the new required state
     */
    public function setRequired($fieldName, $value) {
        return $this->setFieldValue($fieldName, 'required', $value);
    }


    /**
     * Gets whether a field is unique.
     *
     * @param string $fieldName
     * the field name
     *
     * @return bool
     * true if so
     */
    public function isUnique($fieldName) {
        $result = $this->getFieldValue($fieldName, 'unique');
        if ($result === null) {
            $result = false;
        }
        return $result;
    }

    /**
     * Gets the table field of a reference.
     *
     * @param string $fieldName
     * the field name of the reference
     *
     * @return string
     * the table field of a reference or null on invalid field name
     */
    public function getReferenceTable($fieldName) {
        return $this->getReferenceValue($fieldName, 'table');
    }

    /**
     * Gets the name field of a reference.
     *
     * @param string $fieldName
     * the field name of the reference
     *
     * @return string
     * the name field of a reference or null on invalid field name
     */
    public function getReferenceNameField($fieldName) {
        return $this->getReferenceValue($fieldName, 'nameField');
    }


    /**
     * Gets the entity field of a reference.
     *
     * @param string $fieldName
     * the field name of the reference
     *
     * @return string
     * the entity field of a reference or null on invalid field name
     */
    public function getReferenceEntity($fieldName) {
        return $this->getReferenceValue($fieldName, 'entity');
    }

    /**
     * Gets the file path of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the file path of a field or null on invalid field name
     */
    public function getFilePath($fieldName) {
        return $this->getFieldValue($fieldName, 'filepath');
    }

    /**
     * Gets the value of a fixed field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the value of a fixed field or null on invalid field name
     */
    public function getFixedValue($fieldName) {
        return $this->getFieldValue($fieldName, 'fixedvalue');
    }

    /**
     * Sets the value of a fixed field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new value for the fixed field
     */
    public function setFixedValue($fieldName, $value) {
        return $this->setFieldValue($fieldName, 'fixedvalue', $value);
    }

    /**
     * Gets the items of a set field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return array
     * the items of the set field or null on invalid field name
     */
    public function getSetItems($fieldName) {
        return $this->getFieldValue($fieldName, 'setitems');
    }

    /**
     * Gets the step size of a float field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return array
     * the step size of a float field or null on invalid field name
     */
    public function getFloatStep($fieldName) {
        return $this->getFieldValue($fieldName, 'floatStep');
    }

    /**
     * Gets the label of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the label of the field or the field name if no label is set in the CRUD
     * YAML
     */
    public function getFieldLabel($fieldName) {
        $result = $this->getFieldValue($fieldName, 'label');
        if ($result === null && key_exists($fieldName, $this->standardFieldLabels)) {
            $result = $this->standardFieldLabels[$fieldName];
        }
        if ($result === null) {
            $result = $fieldName;
        }
        return $result;
    }

    /**
     * Gets the table where the data is stored.
     *
     * @return @string
     * the table where the data is stored
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Gets the label for the entity.
     *
     * @return @string
     * the label for the entity
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Adds a child to this definition in case the other
     * definition has a reference to this one.
     *
     * @param string $table
     * the table of the referencing definition
     * @param string $fieldName
     * the field name of the referencing definition
     * @param string $entity
     * the entity of the referencing definition
     */
    public function addChild($table, $fieldName, $entity) {
        $this->children[] = array($table, $fieldName, $entity);
    }

    /**
     * Gets the referencing children to this definition.
     *
     * @return array
     * an array with the children referencing the entity. All entries are arrays
     * with three referencing elements: table, fieldName, entity
     */
    public function getChildren() {
        return $this->children;
    }
}
