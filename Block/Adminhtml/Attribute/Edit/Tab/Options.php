<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit\Tab;

use Alekseon\AlekseonEav\Model\Attribute;
use Magento\Store\Model\Store;

/**
 * Class Options
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit\Tab
 */
class Options extends \Magento\Backend\Block\Template
{
    protected $_template = 'Alekseon_AlekseonEav::attribute/edit/options.phtml'; // @codingStandardsIgnoreLine

    /**
     * @var
     */
    private $stores;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Options constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return array
     */
    public function getStores()
    {
        if ($this->stores == null) {

            if ($this->isReadOnly()) {
                $this->stores = [$this->_storeManager->getStore(Store::DEFAULT_STORE_ID)];
            } else {
                $this->stores = $this->_storeManager->getStores(true);
                usort($this->stores, function ($storeA, $storeB) {
                    if ($storeA->getSortOrder() == $storeB->getSortOrder()) {
                        return $storeA->getId() < $storeB->getId() ? -1 : 1;
                    }
                    return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
                });
            }
        }
        return $this->stores;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     */
    public function getOptionValues()
    {
        $result = [];
        $attribute = $this->getAttributeObject();
        $sourceModel = $attribute->getSourceModel();
        $optionValues = false;
        if ($sourceModel) {
            $optionValues = $sourceModel->getAllOptions();
        }
        if (is_array($optionValues)) {
            $defaultValues = $attribute->getDefaultValue();
            if (is_string($defaultValues) && $defaultValues !== '') {
                $defaultValues = [$defaultValues];
            }
            foreach ($optionValues as $value) {
                $id = isset($value['id']) ? $value['id'] : $value['value'];
                $option = [
                   'id' => $id,
                   'option_code' => $sourceModel->getOptionCode($id),
                   'store0' => $value['label'],
                ];

                if (is_array($defaultValues) && in_array($id, $defaultValues)) {
                    $option['checked'] = 'checked';
                }

                if ($storeLabels = $sourceModel->getStoreLabels($id)) {
                    foreach ($storeLabels as $storeId => $storeLabel) {
                        $option['store' . $storeId] = $storeLabel;
                    }
                }
                $result[] = $option;
            }
        }

        return $result;
    }

    /**
     * @return Attribute
     */
    public function getAttributeObject()
    {
        return $this->registry->registry('current_attribute');
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        $attribute = $this->getAttributeObject();
        if (!$attribute->getId()) {
            return false;
        }
        return !$attribute->getInputTypeModel()->canManageOptions();
    }

    /**
     * @return bool
     */
    public function displayOptions()
    {
        $attribute = $this->getAttributeObject();
        if (!$attribute->getId()) {
            return true;
        }
        return $attribute->getInputTypeModel()->usesSource();
    }

    /**
     * @return bool
     */
    public function hasOptionCodes()
    {
        $attribute = $this->getAttributeObject();
        if (!$attribute->getId() || !$attribute->getHasOptionCodes()) {
            return false;
        }
        return $attribute->getInputTypeModel()->hasOptionCodes();
    }
}
