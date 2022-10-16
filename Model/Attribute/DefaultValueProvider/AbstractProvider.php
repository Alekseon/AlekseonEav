<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute;

/**
 * Class AbstractProvider
 * @package Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider
 */
abstract class AbstractProvider extends \Magento\Framework\DataObject
{
    /**
     * @param Attribute $attribute
     * @return bool
     */
    public function canBeUsedForAttribute(Attribute $attribute)
    {
        $applicatbleInputTypes =  $this->getApplicableInputTypes() ?? [];
        if (!in_array($attribute->getFrontendInput(), $applicatbleInputTypes)) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    abstract public function getValue();
}
