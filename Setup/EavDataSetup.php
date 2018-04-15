<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Setup;

use Magento\Framework\DB\Ddl\Table;

/**
 * Class SchemaSetup
 * @package Alekseon\AlekseonEav\Setup
 */
class EavDataSetup implements EavDataSetupInterface
{
    /**
     * @var \Alekseon\AlekseonEav\Model\AttributeRepository | null
     */
    private $attributeRepository;

    /**
     * @param \Alekseon\AlekseonEav\Model\AttributeRepository $attributeRepository
     */
    public function setAttributeRepository(\Alekseon\AlekseonEav\Model\AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return \Alekseon\AlekseonEav\Model\AttributeRepository|null
     * @throws \Exception
     */
    private function getAttributeRepository()
    {
        if ($this->attributeRepository === null) {
            throw new \Exception(__('Attribute Repository is not set.'));
        }
        return $this->attributeRepository;
    }

    /**
     * @param $attributeCode
     * @param $data
     * @param bool $exceptionIfAttributeAlreadyExists
     * @return mixed
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\TemporaryState\CouldNotSaveException
     */
    public function createAttribute($attributeCode, $data, $exceptionIfAttributeAlreadyExists = false)
    {
        $attribute = $this->getAttributeRepository()
                          ->getByAttributeCode($attributeCode, true);
        if ($attribute->getId()) {
            if ($exceptionIfAttributeAlreadyExists) {
                throw new \Exception(__('Cannot create attribute %1, this attribute already exists.', $attributeCode));
            }
            return $attribute;
        }
        $attribute->setData($data);
        $attribute->setAttributeCode($attributeCode);
        $attribute->setIsUserDefined(false);
        $this->getAttributeRepository()->save($attribute);
        return $attribute;
    }

    /**
     * @param $attributeCode
     * @param array $data
     * @param bool $exceptionIfAttributeNotFound
     * @return mixed
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\TemporaryState\CouldNotSaveException
     */
    public function updateAttribute($attributeCode, $data = [], $exceptionIfAttributeNotFound = false)
    {
        $attribute = $this->getAttributeRepository()
                          ->getByAttributeCode($attributeCode, !$exceptionIfAttributeNotFound);

        if ($attribute) {
            $attribute->addData($data);
            $this->getAttributeRepository()->save($attribute);
        }
        return $attribute;
    }

    /**
     * @param $attributeCode
     * @param array $data
     * @return mixed
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\TemporaryState\CouldNotSaveException
     */
    public function createOrUpdateAttribute($attributeCode, $data = [])
    {
        $attribute = $this->createAttribute($attributeCode, $data, false);
        if (!$attribute->isObjectNew()) {
            $this->updateAttribute($attributeCode, $data);
        }
        return $attribute;
    }

    /**
     * @param $attributeCode
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteAttribute($attributeCode)
    {
        $this->getAttributeRepository()->deleteByCode($attributeCode);
    }
}
