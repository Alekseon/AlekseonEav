<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\ResourceModel\Entity;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Alekseon\AlekseonEav\Model\ResourceModel\Entity
 */
abstract class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Attribute table alias prefix
     */
    const ATTRIBUTE_TABLE_ALIAS_PREFIX = 'at_';

    /**
     * @var array
     */
    protected $selectAttributes = [];
    /**
     * @var array
     */
    protected $joinAttributes = [];
    /**
     * @var
     */
    protected $storeId;


    /**
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->storeId)) {
            $this->storeId = $this->getDefaultStoreId();
        }
        return $this->storeId;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Store::DEFAULT_STORE_ID;
    }

    /**
     * @param $attribute
     * @return $this
     * @throws LocalizedException
     */
    public function addAttributeToSelect($attribute)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $a) {
                $this->addAttributeToSelect($a);
            }
            return $this;
        }

        if ('*' === $attribute) {
            $this->getResource()->loadAllAttributes();
            $attributes = $this->getResource()->getAllLoadedAttributes();
            foreach ($attributes as $attributeCode => $attribute) {
                $this->selectAttributes[$attributeCode] = $attribute;
            }
        } else {
            $attributeInstance = $this->getResource()->getAttribute($attribute);
            if (!$attributeInstance) {
                throw new LocalizedException(__('Invalid attribute requested: %1', (string)$attribute));
            }
            $this->selectAttributes[$attribute] = $attributeInstance;
        }

        return $this;
    }

    /**
     * Remove an attribute from selection list
     *
     * @param string $attribute
     * @return $this
     */
    public function removeAttributeToSelect($attribute = null)
    {
        if ($attribute === null) {
            $this->selectAttributes = [];
        } else {
            unset($this->selectAttributes[$attribute]);
        }
        return $this;
    }

    /**
     * @param $table
     * @param array $attributeIds
     * @param array $entityIds
     * @return \Magento\Framework\DB\Select
     */
    protected function getLoadAttributesValuesSelect($table, $attributeIds = [], $entityIds = [])
    {
        $storeIds = [$this->getDefaultStoreId()];
        if ($this->getStoreId()) {
            $storeIds[] = $this->getStoreId();
        }
        $select = $this->getConnection()->select()
            ->from(
                ['e' => $table],
                ['entity_id', 'attribute_id', 'store_id', 'value']
            )->where(
                " e.entity_id IN (?)",
                $entityIds
            )->where(
                " e.store_id IN (?)",
                $storeIds
            )->where(
                'e.attribute_id IN (?)',
                $attributeIds
            );
        return $select;
    }

    /**
     *
     */
    protected function loadAttributes()
    {
        if (empty($this->_data) || empty($this->selectAttributes)) {
            return $this;
        }

        $entityIds = [];
        foreach ($this->_data as $entityData) {
            $entityIds[] = $entityData['entity_id'];
        }

        $tableAttributes = [];
        $attributeCodes = [];
        foreach ($this->selectAttributes as $attribute) {
            if (!$attribute->getId()) {
                continue;
            }
            $tableAttributes[$attribute->getBackendTable()][] = $attribute->getId();
            $attributeCodes[$attribute->getId()] = $attribute->getAttributeCode();
        }

        $selects = [];
        foreach ($tableAttributes as $table => $attributes) {
            $select = $this->getLoadAttributesValuesSelect($table, $attributes, $entityIds);
            $selects[$table] = $select;
        }

        if (!empty($selects)) {
            foreach ($selects as $select) {
                $values = $this->getConnection()->fetchAll($select);
                $this->setItemAttributeValues($values, $attributeCodes);
            }
        }

        return $this;
    }

    /**
     * @param $values
     * @param $attributeCodes
     * @return $this
     */
    protected function setItemAttributeValues($values, $attributeCodes)
    {
        $entitiesValues = [];
        foreach ($values as $value) {
            if (!isset($entitiesValues[$value['entity_id']][$value['attribute_id']])
                || $value['store_id'] != Store::DEFAULT_STORE_ID
            ) {
                $entitiesValues[$value['entity_id']][$value['attribute_id']] = $value;
            }
        }

        foreach ($this->_data as $dataKey => $data) {
            if (isset($entitiesValues[$data['entity_id']])) {
                foreach ($entitiesValues[$data['entity_id']] as $attributeId => $value) {
                    $attributeCode = $attributeCodes[$attributeId];
                    $this->_data[$dataKey][$attributeCode] = $value['value'];
                }
            }
        }

        return $this;
    }

    /**
     * Process loaded collection data
     *
     * @return $this
     */
    protected function _afterLoadData() // @codingStandardsIgnoreLine
    {
        foreach (array_keys($this->_data) as $dataKey) {
            $this->_data[$dataKey]['store_id'] = $this->getStoreId();
        }
        $this->loadAttributes();
        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _renderOrders() // @codingStandardsIgnoreLine
    {
        if (!$this->_isOrdersRendered) {
            foreach ($this->_orders as $attribute => $direction) {
                $this->addAttributeToSort($attribute, $direction);
            }
            $this->_isOrdersRendered = true;
        }

        return $this;
    }

    /**
     * @param $attribute
     * @param string $direction
     * @return $this
     * @throws LocalizedException
     */
    public function addAttributeToSort($attribute, $direction = self::SORT_ORDER_ASC)
    {
        if (!isset($this->selectAttributes[$attribute])) {
            $this->_select->order(new \Zend_Db_Expr($attribute . ' ' . $direction));
            return $this;
        }

        $attributeInstance = $this->selectAttributes[$attribute];
        if (!$attributeInstance->getId()) {
            return $this;
        }

        $this->addAttributeValueJoin($attribute, 'left');
        $orderExpr = [];
        $orderExpr[] = $this->getAttributeTableAlias($attribute) . '.value ' . $direction;

        $this->getSelect()->order($orderExpr);

        return $this;
    }

    /**
     * Add attribute value table to the join if it wasn't added previously
     *
     * @param   string $attributeCode
     * @param   string $joinType inner|left
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function addAttributeValueJoin($attributeCode, $joinType = 'inner')
    {
        if (isset($this->joinAttributes[$attributeCode])) {
            return $this;
        }

        $connection = $this->getConnection();
        $attrTable = $this->getAttributeTableAlias($attributeCode);
        $attribute = $this->getResource()->getAttribute($attributeCode);

        $fKey = 'main_table.entity_id';
        $pKey = $attrTable . '.entity_id';

        if (!$attribute) {
            throw new LocalizedException(__('Invalid attribute name: %1', $attributeCode));
        }

        $attrFieldName = $attrTable . '.value';

        $fKey = $connection->quoteColumnAs($fKey, null);
        $pKey = $connection->quoteColumnAs($pKey, null);

        $condArr = ["{$pKey} = {$fKey}"];
        $condArr[] = $this->getConnection()->quoteInto(
            $connection->quoteColumnAs("{$attrTable}.attribute_id", null) . ' = ?',
            $attribute->getId()
        );

        /**
         * process join type
         */
        $joinMethod = $joinType == 'left' ? 'joinLeft' : 'join';
        $this->joinAttributeToSelect($joinMethod, $attribute, $attrTable, $condArr, $attributeCode, $attrFieldName);

        $this->joinAttributes[$attributeCode]['attribute']= $attribute;

        return $this;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param   string $method
     * @param   object $attribute
     * @param   string $tableAlias
     * @param   array $condition
     * @param   string $fieldCode
     * @param   string $fieldAlias
     * @return $this
     */
    protected function joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        $connection = $this->getConnection();
        $storeId = $this->getStoreId();

        if ($storeId != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            $defCondition = '(' . implode(') AND (', $condition) . ')';
            $defAlias = $tableAlias . '_default';
            $defAlias = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias = str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition .= $connection->quoteInto(
                " AND " . $connection->quoteColumnAs("{$defAlias}.store_id", null) . " = ?",
                $this->getDefaultStoreId()
            );

            $this->getSelect()->{$method}(
                [$defAlias => $attribute->getBackendTable()],
                $defCondition,
                []
            );

            $method = 'joinLeft';
            $fieldAlias = $this->getConnection()->getCheckSql(
                "{$tableAlias}.value_id > 0",
                $fieldAlias,
                $defFieldAlias
            );
            $this->joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->joinAttributes[$fieldCode]['attribute'] = $attribute;
        } else {
            $storeId = $this->getDefaultStoreId();
        }

        $condition[] = $connection->quoteInto(
            $connection->quoteColumnAs("{$tableAlias}.store_id", null) . ' = ?',
            $storeId
        );

        $this->getSelect()->{$method}(
            [$tableAlias => $attribute->getBackendTable()],
            '(' . implode(') AND (', $condition) . ')',
            [$fieldCode => $fieldAlias]
        );

        return $this;
    }

    /**
     * Get alias for attribute value table
     *
     * @param string $attributeCode
     * @return string
     */
    protected function getAttributeTableAlias($attributeCode)
    {
        return $this->getConnection()->getTableName(self::ATTRIBUTE_TABLE_ALIAS_PREFIX . $attributeCode);
    }

    /**
     * @param array|string $attribute
     * @param null $condition
     * @return $this|Collection
     * @throws LocalizedException
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        return $this->addAttributeToFilter($attribute, $condition);
    }

    /**
     * @param $attribute
     * @param null $condition
     * @param string $joinType
     * @return $this
     * @throws LocalizedException
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if (is_array($attribute)) {
            $sqlArr = [];
            foreach ($attribute as $condition) {
                $sqlArr[] = $this->getAttributeConditionSql($condition['attribute'], $condition, $joinType);
            }
            $conditionSql = '(' . implode(') OR (', $sqlArr) . ')';
        } elseif (is_string($attribute)) {
            if ($condition === null) {
                $condition = '';
            }
            $conditionSql = $this->getAttributeConditionSql($attribute, $condition, $joinType);
        }

        if (!empty($conditionSql)) {
            $this->getSelect()->where($conditionSql, null, \Magento\Framework\DB\Select::TYPE_CONDITION);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid attribute identifier for filter (%1)', get_class($attribute))
            );
        }

        return $this;
    }

    /**
     * @param string $field
     * @return mixed|string
     */
    protected function getMappedNonAttributeField($field)
    {
        $mapper = $this->_getMapper();
        if (isset($mapper['fields'][$field])) {
            $field = $mapper['fields'][$field];
        } else {
            $field = 'main_table.' . $field;
        }
        return $field;
    }

    /**
     * @param $attribute
     * @param $condition
     * @param string $joinType
     * @return string
     * @throws LocalizedException
     */
    protected function getAttributeConditionSql($attribute, $condition, $joinType = 'inner')
    {
        if (!$this->hasAttribute($attribute)) {
            $field = $this->getMappedNonAttributeField($attribute);

            $conditionSql = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier($field),
                $condition
            );
            return $conditionSql;
        }

        $this->addAttributeValueJoin($attribute, $joinType);
        if (isset($this->joinAttributes[$attribute]['condition_alias'])) {
            $field = $this->joinAttributes[$attribute]['condition_alias'];
        } else {
            $field = $this->getAttributeTableAlias($attribute) . '.value';
        }
        $conditionSql = $this->_getConditionSql($field, $condition);

        return $conditionSql;
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    protected function hasAttribute($attributeCode)
    {
        if ($this->getResource()->getAttribute($attributeCode)) {
            return true;
        }
        return false;
    }
}
