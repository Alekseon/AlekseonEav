<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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
     * @return string
     */
    public function getValidationFieldClass()
    {
        return 'validate-number';
    }

    /**
     * @param $attribute
     * @return array
     */
    public function getDataValidateParams()
    {
        return [
            'validate-number' => true,
        ];
    }
}
