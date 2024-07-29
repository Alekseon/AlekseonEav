<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model;

use Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\TemporaryState\CouldNotSaveException;

/**
 * Class AttributeRepository
 * @package Alekseon\AlekseonEav\Model
 */
abstract class AttributeRepository
{
    /**
     * @var AttributeFactory
     */
    abstract public function getAttributeFactory();

    /**
     * @param $attributeId
     * @return AttributeInterface
     * @throws NoSuchEntityException
     */
    public function getById($attributeId)
    {
        $attribute = $this->getAttributeFactory()->create();
        $attribute->getResource()->load($attribute, $attributeId);
        if (!$attribute->getId()) {
            throw new NoSuchEntityException(__('Attribute with id "%1" does not exist.', $attributeId));
        }
        return $attribute;
    }

    /**
     * @param $attributeCode
     * @param bool $graceful
     * @return AttributeInterface
     * @throws NoSuchEntityException
     */
    public function getByAttributeCode($attributeCode, $graceful = false)
    {
        $attribute = $this->getAttributeFactory()->create();
        $attribute->getResource()->load($attribute, $attributeCode, 'attribute_code');
        if (!$attribute->getId()) {
            if ($graceful) {
                $attribute->setAttributeCode($attributeCode);
                return $attribute;
            } else {
                throw new NoSuchEntityException(__('Attribute with code "%1" does not exist.', $attributeCode));
            }
        }
        return $attribute;
    }

    /**
     * @param $attributeCode
     * @param bool $graceful
     * @throws NoSuchEntityException
     */
    public function deleteByCode($attributeCode, $graceful = true)
    {
        $attribute = $this->getByAttributeCode($attributeCode, $graceful);
        if ($attribute->getId()) {
            $attribute->delete();
        }
    }

    /**
     * @param $attribute
     */
    public function delete($attribute)
    {
        $attribute->getResource()->delete($attribute);
    }

    /**
     * @param AttributeInterface $attribute
     * @param bool $graceful
     * @return AttributeInterface
     * @throws CouldNotSaveException
     */
    public function save(AttributeInterface $attribute, $graceful = false)
    {
        try {
            $attribute->getResource()->save($attribute);
        } catch (\Exception $exception) {
            if (!$graceful) {
                throw new CouldNotSaveException(__($exception->getMessage()));
            }
        }
        return $attribute;
    }
}
