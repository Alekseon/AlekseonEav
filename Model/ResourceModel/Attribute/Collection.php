<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\ResourceModel\Attribute;

/**
 * Class Collection
 * @package Alekseon\AlekseonEav\Model\ResourceModel\Attribute
 */
abstract class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _initSelect() // @codingStandardsIgnoreLine
    {
        parent::_initSelect();
        $resource = $this->getResource();
        if ($resource->getAdditionalTable()) {
            $fields = $resource->getConnection()->describeTable($resource->getAdditionalTable());
            foreach ($fields as $fieldKey => $fieldData) {
                $fields[$fieldKey] = $fieldKey;
            }
            unset($fields[$resource->getAdditionalTableAttributeIdFieldName()]);
            unset($fields[$resource->getAdditionalTableIdFieldName()]);
            $this->getSelect()->joinLeft(
                ['additiona_table' => $resource->getAdditionalTable()],
                'additiona_table.'
                . $resource->getAdditionalTableAttributeIdFieldName()
                . ' = main_table.'
                . $resource->getIdFieldName(),
                $fields
            );
        }
        return $this;
    }

    /**
     * Redeclare before load method for adding event
     *
     * @return $this
     */
    protected function _beforeLoad() // @codingStandardsIgnoreLine
    {
        $this->addFieldToFilter('entity_type_code', $this->getResource()->getEntityTypeCode());
        return parent::_beforeLoad();
    }
}
