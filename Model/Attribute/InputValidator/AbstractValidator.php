<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

/**
 * Class AbstractValidator
 * @package Alekseon\AlekseonEav\Model\Attribute\InputValidator
 */
class AbstractValidator
{
    /**
     * @param $value
     * @return bool
     */
    public function validateValue($value)
    {
        return true;
    }

    /**
     * @param $attribute
     * @param $adminField
     */
    public function prepareAdminField($attribute, $adminField)
    {
    }

    /**
     * returns parameters used in input attribute "data-validate" on frontend by "WidgetForms" module
     */
    public function getDataValidateParams($attribute)
    {
        return [];
    }
}