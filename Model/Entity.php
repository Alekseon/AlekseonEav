<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
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
     * @var
     */
    protected $defaultValues;

    /**
     * Entity constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Entity $resource
     * @param ResourceModel\Entity\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Alekseon\AlekseonEav\Model\ResourceModel\Entity $resource,
        \Alekseon\AlekseonEav\Model\ResourceModel\Entity\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttribute($attributeCode)
    {
        return $this->getResource()->getAttribute($attributeCode);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->storeId)) {
            $this->storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
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

    public function setAttributeDefaultValues($defaultValues)
    {
        $this->defaultValues = $defaultValues;
        return true;
    }

    public function getAttributeDefaultValue($attributeCode)
    {
        if (is_null($this->defaultValues)) {
            return false;
        }
        return array_key_exists($attributeCode, $this->defaultValues) ? $this->defaultValues[$attributeCode] : false;
    }
}
