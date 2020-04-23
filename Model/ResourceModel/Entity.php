<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\ResourceModel;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\Store;

/**
 * Class Entity
 * @package Alekseon\AlekseonEav\Model\ResourceModel
 */
abstract class Entity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var
     */
    protected $entityTypeCode = 'replace_by_entity_type_code';
    /**
     * @var
     */
    protected $attributeCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var
     */
    protected $attributes = [];
    /**
     * @var array
     */
    protected $notAttributeCode = [];
    /**
     * @var bool
     */
    protected $allAttributesLoaded = false;
    /**
     * @var array
     */
    protected $attributeValuesToSave = [];
    /**
     * @var array
     */
    protected $attributeValuesToDelete = [];
    /**
     * @var string
     */
    protected $imagesDirName = 'alekseon_images';
    /**
     * @var
     */
    protected $stores = [];

    /**
     * Entity constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * @return mixed
     */
    public function getEntityTypeCode()
    {
        return $this->entityTypeCode;
    }

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttribute($attributeCode)
    {
        if (isset($this->notAttributeCode[$attributeCode])) {
            return false;
        }

        if (!isset($this->attributes[$attributeCode])) {
            if ($this->allAttributesLoaded) {
                $this->notAttributeCode[$attributeCode] = false;
                return false;
            }
            $attributeCollection = $this->attributeCollectionFactory->create();
            $attributeCollection->addFieldToFilter('attribute_code', $attributeCode);
            $attribute = $attributeCollection->getFirstItem();
            if ($attribute->getId()) {
                $this->attributes[$attributeCode] = $attribute;
            } else {
                $this->notAttributeCode[$attributeCode] = false;
                return false;
            }
        }
        return $this->attributes[$attributeCode];
    }

    /**
     * @return array
     */
    public function getAllLoadedAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return $this
     */
    public function loadAllAttributes()
    {
        if ($this->allAttributesLoaded) {
            return $this;
        }
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->setOrder('sort_order', AbstractDb::SORT_ORDER_ASC);
        foreach ($attributeCollection as $attribute) {
            $this->attributes[$attribute->getAttributeCode()] = $attribute;
        }
        $this->allAttributesLoaded = true;
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        $this->loadAllAttributes();
        $this->loadItemAttributeValues($object);

        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    private function loadItemAttributeValues($object)
    {

        $tableAttributes = [];
        $attributeCodes = [];
        $attributes = $this->getAllLoadedAttributes();
        foreach ($attributes as $attribute) {
            if (!$attribute->getId()) {
                continue;
            }
            $tableAttributes[$attribute->getBackendTable()][] = $attribute->getId();
            $attributeCodes[$attribute->getId()] = $attribute->getAttributeCode();
        }

        $selects = [];
        foreach ($tableAttributes as $table => $attributeIds) {
            $select = $this->getLoadAttributesValuesSelect($table, $object, $attributeIds);
            $selects[$table] = $select;
        }

        if (!empty($selects)) {
            $attributeDefaultValues = [];
            foreach ($selects as $select) {
                $values = $this->getConnection()->fetchAll($select);
                $attributeValues = $this->getAttributeValues($values, $attributeCodes);
                $attributeDefaultValues = array_merge(
                    $attributeDefaultValues,
                    $this->getAttributeDefaultValues($values, $attributeCodes)
                );
                $object->addData($attributeValues);
            }
            $object->setAttributeDefaultValues($attributeDefaultValues);
        }

        foreach ($attributes as $attribute) {
            $backendModel = $attribute->getBackendModel();
            if ($backendModel) {
                $backendModel->afterLoad($object);
            }
        }

        return $this;
    }

    /**
     * @param $object
     * @param $attribute
     * @return array
     */
    public function getAllAttributeValues($object, $attribute)
    {
        $backendTable = $attribute->getBackendTable();
        $select = $this->getLoadAttributesValuesSelect($backendTable, $object, [$attribute->getId()], false);
        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param $values
     * @param $attributeCodes
     * @return array
     */
    private function getAttributeValues($values, $attributeCodes)
    {
        $attributeValues = [];
        foreach ($values as $value) {
            $attributeCode = $attributeCodes[$value['attribute_id']];
            if (!isset($attributeValues[$attributeCode]) || $value['store_id'] != Store::DEFAULT_STORE_ID) {
                $attributeValues[$attributeCode] = $value['value'];
            }
        }
        return $attributeValues;
    }

    /**
     * @param $values
     * @param $attributeCodes
     * @return array
     */
    private function getAttributeDefaultValues($values, $attributeCodes)
    {
        $attributeDefaultValues = [];
        foreach ($values as $value) {
            $attributeCode = $attributeCodes[$value['attribute_id']];
            if ($value['store_id'] == Store::DEFAULT_STORE_ID) {
                $attributeDefaultValues[$attributeCode] = $value['value'];
            }
        }
        return $attributeDefaultValues;
    }

    /**
     * @param $table
     * @param array $attributeIds
     * @param $entity
     * @param null $storeIds
     * @return \Magento\Framework\DB\Select
     */
    private function getLoadAttributesValuesSelect($table, $entity, $attributeIds = [], $storeIds = null)
    {
        $entityId = $entity->getEntityId();
        if ($storeIds === null) {
            $storeIds = [Store::DEFAULT_STORE_ID];
            if ($entity->getStoreId()) {
                $storeIds[] = $entity->getStoreId();
            }
        }

        $select = $this->getConnection()->select()
            ->from(
                ['e' => $table],
                ['entity_id', 'attribute_id', 'store_id', 'value']
            )->where(
                " e.entity_id = ?",
                $entityId
            )->where(
                'e.attribute_id IN (?)',
                $attributeIds
            );

        if ($storeIds) {
            $select->where(
                " e.store_id IN (?)",
                $storeIds
            );
        }

        return $select;
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        if ($object->getId()) {
            $attributesUseDefault = $object->getUseDefault();
            if (is_array($attributesUseDefault)) {
                foreach ($attributesUseDefault as $attributeCode) {
                    $object->setData($attributeCode, null);
                }
            }
        }
        return $this;
    }


    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        $this->loadAllAttributes();
        $attributes = $this->getAllLoadedAttributes();
        $attributesToSave = [];
        foreach ($attributes as $attribute) {
            if ($attribute->isAttributeValueUpdated($object)) {
                $this->prepareAttributeForSave($object, $attribute);
                $attributesToSave[] = $attribute;
            }
        }

        $this->saveAttributeValues();
        foreach ($attributesToSave as $attribute) {
            $backendModel = $attribute->getBackendModel();
            if ($backendModel) {
                $backendModel->afterSave($object);
            }
        }
        return $this;
    }

    /**
     * @param $object
     * @param $attribute
     * @return $this
     * @throws \Exception
     */
    protected function prepareAttributeForSave($object, $attribute)
    {
        $backendModel = $attribute->getBackendModel();
        if ($backendModel) {
            $backendModel->beforeSave($object);
        }

        $table = $attribute->getBackendTable();

        if (!isset($this->attributeValuesToDelete[$table])) {
            $this->attributeValuesToDelete[$table] = [];
        }
        if (!isset($this->attributeValuesToSave[$table])) {
            $this->attributeValuesToSave[$table] = [];
        }

        $value = $this->prepareValueForSave($object, $attribute);

        $data = [
            'entity_id' => $object->getEntityId(),
            'attribute_id' => $attribute->getId(),
            'value' => $value,
        ];

        switch ($attribute->getScope()) {
            case Scopes::SCOPE_STORE:
                $data['store_id'] = [$object->getStoreId()];
                break;
            case Scopes::SCOPE_WEBSITE:
                $store = $this->getStore($object->getStoreId());
                $data['store_id'] = $store->getWebsite()->getStoreIds();
                break;
            case Scopes::SCOPE_GLOBAL:
            default:
                $data['store_id'] = [Store::DEFAULT_STORE_ID];
                break;
        }

        if (is_array($data['store_id'])) {
            if ($value === null) {
                $this->attributeValuesToDelete[$table][] = $data;
            } else {
                $storeIds = $data['store_id'];
                foreach ($storeIds as $storeId) {
                    $data['store_id'] = $storeId;
                    $this->attributeValuesToSave[$table][] = $data;
                }
            }
        }

        return $this;
    }

    /**
     * @param $value
     * @param $attribute
     * @return mixed
     * @throws \Exception
     */
    private function prepareValueForSave($object, $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $value = $object->getData($attributeCode);
        if ($attribute->getIsRequired()) {
            // check if there is value of required attribute, its checked only on saving default values
            if ($object->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                if ($value === null || $value == '' || (is_array($value) && empty($value))) {
                    throw new \Exception(__('Value for attribute "%1" is required.', $attribute->getFrontendLabel()));
                }
            }
        }
        return $value;
    }

    /**
     * Save and delete collected attribute values
     *
     * @return $this
     */
    protected function saveAttributeValues()
    {
        $connection = $this->getConnection();

        foreach ($this->attributeValuesToSave as $table => $data) {
            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['value']);
            }
        }

        foreach ($this->attributeValuesToDelete as $table => $datas) {
            foreach ($datas as $data) {
                if (!empty($data)) {
                    $connection->delete(
                        $table,
                        [
                            'entity_id = ?' => $data['entity_id'],
                            'store_id IN (?)' => $data['store_id'],
                            'attribute_id = ?' => $data['attribute_id'],
                        ]
                    );
                }
            }
        }
        // reset data arrays
        $this->attributeValuesToSave = [];
        $this->attributeValuesToDelete = [];

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        if (!$object->getEntityId()) {
            return $this;
        }

        $connection = $this->getConnection();
        $this->loadAllAttributes();
        $attributes = $this->getAllLoadedAttributes();

        $tableAttributes = [];
        foreach ($attributes as $attribute) {
            $tableAttributes[$attribute->getBackendTable()][] = $attribute->getId();
            $backendModel = $attribute->getBackendModel();
            if ($backendModel) {
                $backendModel->beforeDelete($object);
            }
        }

        foreach ($tableAttributes as $table => $attributeIds) {
            $connection->delete($table, ['entity_id =?' => $object->getEntityId()]);
        }

        foreach ($attributes as $attribute) {
            $backendModel = $attribute->getBackendModel();
            if ($backendModel) {
                $backendModel->afterDelete($object);
            }
        }

        return $this;
    }

    /**
     * @param $storeId
     * @return mixed
     */
    private function getStore($storeId)
    {
        if (!isset($this->stores[$storeId])) {
            $this->stores[$storeId] = $this->storeManager->getStore($storeId);
        }
        return $this->stores[$storeId];
    }

    /**
     * @return string
     */
    public function getImagesDirName()
    {
        return $this->imagesDirName;
    }
}
