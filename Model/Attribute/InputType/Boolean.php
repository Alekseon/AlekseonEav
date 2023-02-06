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
    private $yesNoSource;

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
        if ($this->getDefaultValue() !== false) {
            return false;
        }
        return true;
    }

    /**
     * @return true
     */
    public function isRequred()
    {
        return true;
    }
}
