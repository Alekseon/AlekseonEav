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
class Email extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validateValue($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @param $adminField
     */
    public function prepareAdminField($attribute, $adminField)
    {
        $adminField->setType('email');
    }

    /**
     * @param $attribute
     * @return array
     */
    public function getDataValidateParams($attribute)
    {
        return [
            'validate-email' => true,
        ];
    }
}
