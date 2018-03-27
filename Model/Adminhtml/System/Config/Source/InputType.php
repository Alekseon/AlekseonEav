<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

/**
 * Class InputType
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
class InputType implements \Magento\Framework\Option\ArrayInterface
{
    const INPUT_TYPE_TEXT = 'text';
    const INPUT_TYPE_TEXTAREA = 'textarea';
    const INPUT_TYPE_BOOLEAN = 'boolean';
    const INPUT_TYPE_SELECT = 'select';
    const INPUT_TYPE_MULTISELECT = 'multiselect';
    const INPUT_TYPE_DATE = 'date';
    const INPUT_TYPE_IMAGE = 'image';

    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
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
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::INPUT_TYPE_TEXT => __('Text Field'),
            self::INPUT_TYPE_TEXTAREA => __('Text Area'),
            self::INPUT_TYPE_BOOLEAN => __('Yes/No'),
            self::INPUT_TYPE_SELECT => __('Dropdown'),
            self::INPUT_TYPE_MULTISELECT => __('Multiple Select'),
            self::INPUT_TYPE_DATE => __('Date'),
            self::INPUT_TYPE_IMAGE => __('Image'),
        ];
    }
}
