<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model;

use \Alekseon\AlekseonEav\Api\Data\EntityInterface;

/**
 * Class Entity
 * @package Alekseon\AlekseonEav\Model
 */
abstract class Entity extends \Magento\Framework\Model\AbstractModel implements EntityInterface
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var ResourceModel\Entity
     */
    private $entityResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var
     */
    protected $defaultValues;

    /**
     * Entity constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Entity $resource
     * @param ResourceModel\Entity\Collection $resourceCollection
     */
    public function __construct(
        \Alekseon\AlekseonEav\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Alekseon\AlekseonEav\Model\ResourceModel\Entity $resource,
        \Alekseon\AlekseonEav\Model\ResourceModel\Entity\Collection $resourceCollection
    ) {
        $this->entityResource = $resource;
        $this->storeManager = $context->getStoreManager();
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * @param string $attributeCode
     * @return Attribute
     */
    public function getAttribute($attributeCode)
    {
        return $this->entityResource->getAttribute($attributeCode);
    }

    /**
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->entityResource = $resource;
        $this->_resource = $resource;
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->storeId)) {
            $this->storeId = $this->getData('store_id');
        }
        if (is_null($this->storeId)) {
            if ($this->_appState->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND) {
                $this->storeId = $this->storeManager->getStore()->getId();
            } else {
                $this->storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }
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
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        return $this->storeManager->getStore($this->getStoreId());
    }

    /**
     * @param $defaultValues
     * @return bool
     */
    public function setAttributeDefaultValues($defaultValues)
    {
        $this->defaultValues = $defaultValues;
        return true;
    }

    /**
     * @param $attributeCode
     * @return string | false
     */
    public function getAttributeDefaultValue($attributeCode)
    {
        if (is_null($this->defaultValues)) {
            return false;
        }
        return array_key_exists($attributeCode, $this->defaultValues) ? $this->defaultValues[$attributeCode] : null;
    }

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttributeText($attributeCode)
    {
        $value = $this->getData($attributeCode);;
        $attribute = $this->getAttribute($attributeCode);
        if (!$attribute) {
            return $value;
        }
        return $attribute->getObjectValueAsText($this);
    }

    /**
     * @param $attributeCode
     */
    public function saveAttributeValue($attributeCode)
    {
        $attribute = $this->getAttribute($attributeCode);
        if ($attribute) {
            $this->entityResource->saveAttributeValue($this, $attribute);
        }
        return $this;
    }

    /**
     * Get system Values
     *
     * @return array
     */
    public function getSystemValues()
    {
        return [
            //'attribute_code' => [
            //    'provider' => \Alekseon\AlekseonEav\Model\Attribute\SystemValueProvider\ScopeConfigValue::class,
            //    'use_label' => '', // optional
            //    'params' => ['path' => ''] // optional
            //],
        ];
    }
}
