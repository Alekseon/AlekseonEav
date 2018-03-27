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
    private $storeLabels = [];

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $optionValues = $this->getAttribute()
            ->getResource()
            ->getAttributeOptionValues($this->getAttribute());

        $options = [];

        foreach ($optionValues as $optionValue) {
            $options[$optionValue->getOptionId()] = $optionValue->getLabel();
            $this->storeLabels[$optionValue->getOptionId()] = $optionValue->getStoreLabels();
        }

        return $options;
    }

    public function getStoreLabels($optionId)
    {
        $this->getAllOptions(); // to be sure that options have been loaded
        if (isset($this->storeLabels[$optionId])) {
            return $this->storeLabels[$optionId];
        } else {
            return false;
        }
    }
}
