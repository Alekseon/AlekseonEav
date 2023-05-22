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
class AbstractValidator extends \Magento\Framework\DataObject
{
    /**
     * @var
     */
    protected $attribute;

    /**
     *
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @param $value
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validateValue($value)
    {
        return true;
    }

    /**
     * @param $adminField
     */
    public function prepareAdminField($adminField)
    {
        $fieldClass = $adminField->getClass();
        if ($this->getValidationFieldClass()) {
            $fieldClass .= ' ' . $this->getValidationFieldClass();
            $adminField->setClass($fieldClass);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getValidationFieldClass()
    {
        return false;
    }

    /**
     * returns parameters used in input attribute "data-validate" on frontend by "WidgetForms" module
     */
    public function getDataValidateParams()
    {
        return [];
    }
}
