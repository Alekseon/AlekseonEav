<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

/**
 * Class AbstractBackendType
 * @package Alekseon\AlekseonEav\Model\Attribute\BackendType
 */
class Boolean extends Select
{
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean
     */
    protected $yesNoSource;
    /**
     * @var string
     */
    protected $backendModel = 'Alekseon\AlekseonEav\Model\Attribute\Backend\Boolean';

    /**
     * Boolean constructor.
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean $yesNoSource
     */
    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory,
        \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean $yesNoSource
    ) {
        $this->yesNoSource = $yesNoSource;
        parent::__construct($universalFactory, $optionFactory);
    }

    /**
     * @return \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean
     */
    public function getSourceModel()
    {
        return $this->yesNoSource;
    }

    /**
     * @return false
     */
    public function hasEmptyOption()
    {
        return false;
    }

    /**
     * @return array|false|mixed|void|null
     */
    public function getDefaultValue()
    {
        $defaultValue = $this->getAttribute()->getData('default_value');
        if ($defaultValue !== \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean::VALUE_YES) {
            $defaultValue = \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean::VALUE_NO;
        }
        return $defaultValue;
    }
}
