<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

/**
 * Class Number
 * @package Alekseon\AlekseonEav\Model\Attribute\InputValidator
 */
class Number extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validateValue($value)
    {
        return !$value || is_numeric($value);
    }

    /**
     * @param $attribute
     * @param $adminField
     */
    public function prepareAdminField($attribute, $adminField)
    {
        $class = $adminField->getClass();
        $class .= ' validate-number';
        $adminField->setClass($class);
    }

    /**
     * @param $attribute
     * @return array
     */
    public function getDataValidateParams($attribute)
    {
        return [
            'validate-number' => true,
        ];
    }
}
