<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

/**
 * Class AbstractSource
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
abstract class AbstractSource implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var
     */
    protected $attribute;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var bool
     */
    protected $hasOptions = false;

    /**
     * @return array
     */
    abstract public function getOptionArray(): array;

    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $optionArray = [];
        $options = $this->getOptionArray();
        foreach ($options as $optionCode => $optionLabel) {
            $optionArray[] = [
                'value' => $optionCode,
                'label' => $optionLabel,
            ];
        }
        return $optionArray;
    }

    /**
     *
     */
    public function hasOptions()
    {
        $this->getOptionArray();
        return $this->hasOptions;
    }

    /**
     * @param $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        $this->options = null;
        $this->hasOptions = false;
        return $this;
    }
}
