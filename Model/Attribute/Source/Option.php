<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class Boolean
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class Option extends AbstractSource
{
    /**
     * @var array
     */
    private $storeLabels = [];
    /**
     * @var array
     */
    private $optionValues = [];
    /**
     * @var array
     */
    private $optionCodes = [];

    /**
     * @param null $storeId
     * @return array|mixed
     */
    public function getOptions($storeId = null)
    {
        $attributeOptionValues = $this->getAttribute()
            ->getResource()
            ->getAttributeOptionValues($this->getAttribute());

        $options = [];

        foreach ($attributeOptionValues as $optionValue) {
            $optionId = $optionValue->getOptionId();
            $options[$optionId] = $this->getStoreLabel($optionId, $storeId);
            $this->storeLabels[$optionId] = $optionValue->getStoreLabels();
            $this->optionCodes[$optionId] = $optionValue->getOptionCode();
            $this->optionValues[$optionId] = $optionValue;
        }

        return $options;
    }

    /**
     * @param $optionId
     * @return bool|mixed
     */
    public function getStoreLabels($optionId)
    {
        $this->getAllOptions(); // to be sure that options have been loaded
        if (isset($this->storeLabels[$optionId])) {
            return $this->storeLabels[$optionId];
        } else {
            return false;
        }
    }

    /**
     * @param $optionId
     * @param $storeId
     * @return null
     */
    public function getStoreLabel($optionId, $storeId = null)
    {
        $storeLabels = $this->getStoreLabels($optionId);
        if (!isset($this->optionValues[$optionId])) {
            return null;
        }
        $optionValue = $this->optionValues[$optionId];
        if ($storeId  && isset($storeLabels[$storeId])) {
            return $storeLabels[$storeId];
        } else {
            return $optionValue->getLabel();
        }
    }

    /**
     * @param $optionId
     * @return string
     */
    public function getOptionCode($optionId)
    {
        if (isset($this->optionCodes[$optionId])) {
            return $this->optionCodes[$optionId];
        }
        return '';
    }
}
