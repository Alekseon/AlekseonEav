<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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
    public function prepareAdminField($adminField)
    {
        $adminField->setType('email');
        return parent::prepareAdminField($adminField);
    }

    /**
     * @param $attribute
     * @return array
     */
    public function getDataValidateParams()
    {
        return [
            'email' => true,
        ];
    }
}
