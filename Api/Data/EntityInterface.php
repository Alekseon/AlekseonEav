<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Api\Data;

/**
 * Interface EntityInterface
 * @package Alekseon\AlekseonEav\Api\Data
 */
interface EntityInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $value
     * @return mixed
     */
    public function setId($value);

    /**
     * @param $key
     * @param null $value
     * @return mixed
     */
    public function setData($key, $value = null);

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttribute($attributeCode);

    /**
     * @return mixed
     */
    public function getStoreId();

    /**
     * @param $storeId
     * @return mixed
     */
    public function setStoreId($storeId);

    /**
     * @return mixed
     */
    public function getStore();

    /**
     * @param $defaultValues
     * @return mixed
     */
    public function setAttributeDefaultValues($defaultValues);

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttributeDefaultValue($attributeCode);

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttributeText($attributeCode);

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function saveAttributeValue($attributeCode);
}
