<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

/**
 * Class Scopes
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
class Scopes implements \Magento\Framework\Option\ArrayInterface
{
    const SCOPE_GLOBAL = 0;
    const SCOPE_WEBSITE = 1;
    const SCOPE_STORE = 2;

    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SCOPE_GLOBAL, 'label' => __('Global')],
            ['value' => self::SCOPE_WEBSITE, 'label' => __('Website')],
            ['value' => self::SCOPE_STORE, 'label' => __('Store View')],
        ];
    }

    public function getOptionArray()
    {
        return [
            self::SCOPE_GLOBAL => __('Global'),
            self::SCOPE_WEBSITE => __('Website'),
            self::SCOPE_STORE => __('Store View'),
        ];
    }
}
