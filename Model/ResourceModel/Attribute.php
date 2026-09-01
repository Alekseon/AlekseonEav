<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\ResourceModel;

use Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

/**
 * Class Attribute
 * @package Alekseon\AlekseonEav\Model\ResourceModel
 */
abstract class Attribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Attribute code is used to build SQL table aliases, HTML field names and entity data keys,
     * so it has to be limited to the same charset the admin form validates on the client side
     * ("validate-code" rule).
     */
    const ATTRIBUTE_CODE_PATTERN = '/^[a-z][a-z0-9_]*\z/';
    /**
     * @var
     */
    protected $entityTypeCode = 'replace_by_entity_type_code';
    /**
     * Main table of the entity this attribute type belongs to. When set, its columns are treated
     * as reserved attribute codes, so that an attribute value cannot overwrite an entity column.
     *
     * @var string|null
     */
    protected $entityTable = null;
    /**
     * Attribute codes that collide with keys used by the entity save flow.
     *
     * @var string[]
     */
    protected $reservedAttributeCodes = [
        'entity_id',
        'store_id',
        'use_default',
    ];
    /**
     * @var string
     */
    protected $mainTable = 'alekseon_eav_attribute';
    /**
     * @var string
     */
    protected $backendTablePrefix = 'alekseon_eav_entity';
    /**
     * @var string
     */
    protected $additionalAttributeTable = null;
    /**
     * @var string
     */
    protected $additionalTableAttributeIdFieldName = 'attribute_id';
    /**
     * @var string
     */
    protected $additionalTableIdFieldName = 'id';
    /**
     * @var string
     */
    protected $attributeOptionTable = 'alekseon_eav_attribute_option';
    /**
     * @var string
     */
    protected $attributeOptionValueTable = 'alekseon_eav_attribute_option_value';
    /**
     * @var string
     */
    protected $attributeFrontendLabelsTable = 'alekseon_eav_attribute_frontend_label';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var array
     */
    protected $_serializableFields = ['attribute_extra_params' => [[], []]];

    /**
     * Attribute constructor.
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
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init($this->mainTable, 'id');
    }

    /**
     * @return mixed
     */
    public function getEntityTypeCode()
    {
        return $this->entityTypeCode;
    }

    /**
     * @return string
     */
    public function getBackendTablePrefix()
    {
        return $this->backendTablePrefix;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        $object->setEntityTypeCode($this->getEntityTypeCode());

        // legacy attributes are left alone, only a new or changed code has to pass the validation
        if ($object->isObjectNew() || $object->dataHasChangedFor('attribute_code')) {
            $this->validateAttributeCode($object);
        }

        if ($object->isObjectNew()) {
            if (!$object->getFrontendInput()) {
                $object->setFrontendInput(
                    \Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository::DEFAULT_INPUT_TYPE_CODE
                );
            }
            if (!$object->getBackendType()) {
                $object->setBackendType($object->getInputTypeModel()->getDefaultBackendType());
            }
            if ($object->getIsUserDefined() === null) {
                $object->setIsUserDefined(true);
            }
        }

        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Validate attribute code on the server side, the "validate-code" class in the admin form
     * is only a client side check
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function validateAttributeCode(\Magento\Framework\Model\AbstractModel $object)
    {
        $attributeCode = $object->getAttributeCode();
        $attributeCode = is_scalar($attributeCode) ? trim((string) $attributeCode) : '';
        $object->setAttributeCode($attributeCode);

        if (!preg_match(self::ATTRIBUTE_CODE_PATTERN, $attributeCode)) {
            throw new LocalizedException(
                __(
                    'Attribute code "%1" is invalid. Please use only lowercase letters (a-z), numbers (0-9) '
                    . 'or underscore (_), and the first character should be a letter.',
                    $attributeCode
                )
            );
        }

        if (strlen($attributeCode) > AttributeInterface::ATTRIBUTE_CODE_MAX_LENGTH) {
            throw new LocalizedException(
                __(
                    'Attribute code must not be more than %1 characters.',
                    AttributeInterface::ATTRIBUTE_CODE_MAX_LENGTH
                )
            );
        }

        if (in_array($attributeCode, $this->getReservedAttributeCodes(), true)) {
            throw new LocalizedException(
                __('Attribute code "%1" is reserved by the system, please use another one.', $attributeCode)
            );
        }

        return $this;
    }

    /**
     * Attribute codes that cannot be used, because they would collide with entity columns
     *
     * @return string[]
     */
    protected function getReservedAttributeCodes()
    {
        $reservedAttributeCodes = $this->reservedAttributeCodes;

        if ($this->entityTable) {
            $reservedAttributeCodes = array_merge(
                $reservedAttributeCodes,
                array_keys($this->getConnection()->describeTable($this->getTable($this->entityTable)))
            );
        }

        return $reservedAttributeCodes;
    }

    /**
     * @return bool|string
     */
    public function getAdditionalTable()
    {
        if ($this->additionalAttributeTable) {
            return $this->getTable($this->additionalAttributeTable);
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getAdditionalTableIdFieldName()
    {
        return $this->additionalTableIdFieldName;
    }

    /**
     * @return string
     */
    public function getAdditionalTableAttributeIdFieldName()
    {
        return $this->additionalTableAttributeIdFieldName;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        if ($this->getAdditionalTable()) {
            $select = $this->getConnection()->select()
                ->from($this->getAdditionalTable())
                ->where($this->getAdditionalTableAttributeIdFieldName() . '=?', $object->getId());
            $data = $this->getConnection()->fetchRow($select);
            if ($data) {
                unset($data[$this->getAdditionalTableIdFieldName()]);
                unset($data[$this->getAdditionalTableAttributeIdFieldName()]);
                $object->addData($data);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        if ($this->getAdditionalTable()) {
            $condition = $this->getConnection()->quoteInto(
                $this->getAdditionalTableAttributeIdFieldName() . '=?',
                $object->getId()
            );
            $data = $this->_prepareDataForTable($object, $this->getAdditionalTable());
            $data[$this->getAdditionalTableAttributeIdFieldName()] = $object->getId();
            $select = $this->getConnection()->select()->from(
                $this->getAdditionalTable()
            )->where(
                $condition
            );
            if ($this->getConnection()->fetchOne($select) !== false) {
                $this->getConnection()->update($this->getAdditionalTable(), $data, $condition);
            } else {
                $this->getConnection()->insert($this->getAdditionalTable(), $data);
            }
        }
        if ($object->getInputTypeModel()->getSourceModel()) {
            $this->processAttributeOptions($object);
        }
        $this->processFrontendLabels($object);

        return $this;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @param \Alekseon\AlekseonEav\Model\Attribute $object
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processAttributeOptions(\Alekseon\AlekseonEav\Model\Attribute $object)
    {
        $default = $object->getDefault() ?: [];
        $optionsData = $object->getOption();

        $defaultValue = [];
        if ($object->getData('default_value')) {   // it means there is default value provider and we set it as first default value
            $defaultValue[] = $object->getData('default_value');
        }

        $optionsValues = $optionsData['value'] ?? [];

        if (!empty($optionsValues)) {
            foreach ($optionsValues as $optionId => $values) {
                $updatedOptionId = $this->updateAttributeOption($object, $optionId, $optionsData);
                if ($updatedOptionId === false) {
                    continue;
                }

                $this->updateAttributeOptionValues($updatedOptionId, $values);

                if (in_array($optionId, $default)) {
                    $defaultValue[] = $updatedOptionId;
                }
            }
        } else {
            $options = $object->getSourceModel()->getOptions();
            foreach (array_keys($options) as $optionId) {
                if (in_array($optionId, $default)) {
                    $defaultValue[] = $optionId;
                }
            }
        }

        $this->_saveDefaultValue($object, $defaultValue);
    }

    /**
     * @param $object
     * @param $defaultValue
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _saveDefaultValue($object, $defaultValue)
    {
        if ($defaultValue !== null) {
            $bind = ['default_value' => implode(',', $defaultValue)];
            $where = ['id = ?' => $object->getId()];
            $this->getConnection()->update($this->getMainTable(), $bind, $where);
        }
    }

    /**
     * @param $object
     * @param $optionId
     * @param $optionsData
     * @return int
     */
    private function updateAttributeOption($object, $optionId, $optionsData)
    {
        $connection = $this->getConnection();
        $table = $this->getTable($this->attributeOptionTable);

        $intOptionId = is_numeric($optionId) ? (int)$optionId : 0;

        $select = $connection->select()
            ->from($table)
            ->where('option_id =?', $intOptionId)
            ->where('attribute_id =?', $object->getId());
        if (!$connection->fetchOne($select)) {
            $intOptionId = false;
        }

        $sortOrder = empty($optionsData['order'][$optionId]) ? 0 : $optionsData['order'][$optionId];
        $optionCode = empty($optionsData['option_code'][$optionId]) ? '' : $optionsData['option_code'][$optionId];

        if (!empty($optionsData['delete'][$optionId])) {
            if ($intOptionId) {
                $connection->delete($table, ['option_id = ?' => $intOptionId]);
            }
            return false;
        }

        if (!$intOptionId) {
            $data = [
                'attribute_id' => $object->getId(),
                'sort_order' => $sortOrder,
                'option_code' => $optionCode,
            ];
            $connection->insert($table, $data);
            $intOptionId = $connection->lastInsertId($table);
        } else {
            $data = [
                'sort_order' => $sortOrder,
                'option_code' => $optionCode,
            ];
            $where = ['option_id = ?' => $intOptionId];
            $connection->update($table, $data, $where);
        }

        return $intOptionId;
    }

    /**
     * @param $optionId
     * @param $values
     */
    protected function updateAttributeOptionValues($optionId, $values)
    {
        $connection = $this->getConnection();
        $table = $this->getTable($this->attributeOptionValueTable);

        $connection->delete($table, ['option_id = ?' => $optionId]);

        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            $storeId = $store->getId();
            if (!empty($values[$storeId]) || isset($values[$storeId]) && $values[$storeId] == '0') {
                $data = ['option_id' => $optionId, 'store_id' => $storeId, 'value' => $values[$storeId]];
                $connection->insert($table, $data);
            }
        }
    }

    /**
     * @param $object
     * @return array
     */
    public function getAttributeOptionValues($object)
    {
        $connection = $this->getConnection();
        $optionsSelect = $connection
            ->select()
            ->from($this->getTable($this->attributeOptionTable))
            ->where('attribute_id = ?', $object->getId())
            ->order(
                'sort_order ASC'
            );
        $options = $connection->fetchAll($optionsSelect);
        $result = [];
        $optionIds = [];
        foreach ($options as $option) {
            $optionId = $option['option_id'];
            $optionIds[] = $optionId;
            $result[$optionId] = new DataObject($option);
        }

        $valuesSelect = $connection
            ->select()
            ->from($this->getTable($this->attributeOptionValueTable))
            ->where('option_id IN (?)', $optionIds);
        $values = $connection->fetchAll($valuesSelect);

        foreach ($values as $value) {
            $optionId = $value['option_id'];
            $valueStoreId = $value['store_id'];
            $option = $result[$optionId];
            $storeLabels = $option->getStoreLabels() ? $option->getStoreLabels() : [];
            $storeLabels[$valueStoreId] = $value['value'];
            $option->setStoreLabels($storeLabels);
            if ($valueStoreId == Store::DEFAULT_STORE_ID) {
                $option->setLabel($value['value']);
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object) // @codingStandardsIgnoreLine
    {
        if ($this->getAdditionalTable()) {
            $condition = $this->getConnection()->quoteInto(
                $this->getAdditionalTableAttributeIdFieldName() . '=?',
                $object->getId()
            );
            $this->getConnection()->delete($this->getAdditionalTable(), $condition);
        }
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object) // @codingStandardsIgnoreLine
    {
        $field = $this->getConnection()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable())
            ->where($field . '=?', $value)
            ->where('entity_type_code' . '=?', $this->getEntityTypeCode());
        return $select;
    }

    /**
     * @param $object
     */
    private function processFrontendLabels($object)
    {
        $frontendLabels = $object->getFrontendLabels();

        if (!is_array($frontendLabels)) {
            return $this;
        }

        $connection = $this->getConnection();
        $table = $this->getTable($this->attributeFrontendLabelsTable);
        $currentFrontendLabels = $this->getFrontendLabels($object);

        foreach($frontendLabels as $storeId => $label) {
            if (isset($currentFrontendLabels[$storeId])) {
                $currentLabel = $currentFrontendLabels[$storeId];
                if ($currentLabel['label'] == $label) {
                    // label has not been changed
                } else if (!$label) {
                    // label is empty, so it can be removed
                    $connection->delete($table, ['id = ?' => $currentLabel['id']]);
                } else {
                    $data = ['label' => $label];
                    $where = ['id = ?' => $currentLabel['id']];
                    $connection->update($table, $data, $where);
                }
            } else {
                if ($label) {
                    $data = ['attribute_id' => (int)$object->getId(), 'store_id' => $storeId, 'label' => $label];
                    $connection->insert($table, $data);
                }
            }
        }
    }

    /**
     * @param $object
     * @param bool $asObjects
     * @return array
     */
    public function getFrontendLabels($object, $asObjects = true)
    {
        $connection = $this->getConnection();
        $frontendLabelsSelect = $connection
            ->select()
            ->from($this->getTable($this->attributeFrontendLabelsTable))
            ->where('attribute_id = ?', $object->getId());
        $frontendLabels = $connection->fetchAll($frontendLabelsSelect);
        $result = [];
        foreach($frontendLabels as $frontendLabel) {
            if ($asObjects) {
                $result[$frontendLabel['store_id']] = $frontendLabel;
            } else {
                $result[$frontendLabel['store_id']] = $frontendLabel['label'];
            }
        }
        return $result;
    }
}
