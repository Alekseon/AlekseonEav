<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

/**
 * Class MaxLength
 * @package Alekseon\AlekseonEav\Model\Attribute\InputValidator
 */
class MaxLength extends AbstractValidator
{
    /**
     * @var string
     */
    protected $code = 'max_length';

    /**
     * @param $value
     * @return bool
     */
    public function validateValue($value)
    {
        $maxLength = $this->attribute->getInputParam('maxLength');
        if ($maxLength && strlen($value) > $maxLength) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getValidationFieldClass()
    {
        $maxLength = $this->attribute->getInputParam('maxLength');
        return $maxLength ? 'validate-length maximum-length-' . $maxLength : '';
    }

    /**
     * @return bool[]
     */
    public function getDataValidateParams()
    {
        $maxLength = $this->attribute->getInputParam('maxLength');
        if ($maxLength) {
            return [
                'validate-length' => true,
            ];
        }

        return [];
    }
}
