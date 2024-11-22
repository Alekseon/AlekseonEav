<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

use Magento\Framework\Data\Form\Element\AbstractElement;

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
     * @var string
     */
    protected $code = '';

    /**
     *
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
     * @return array
     */
    public function getInputParams()
    {
        return [];
    }

    /**
     * @param AbstractElement $adminField
     * @return $this
     */
    public function prepareAdminField(AbstractElement $adminField)
    {
        $fieldClass = $adminField->getClass();
        if ($this->getValidationFieldClass()) {
            $fieldClass .= ' ' . $this->getValidationFieldClass();
            $adminField->setClass($fieldClass);
        }
        if ($this->getAdminDataValidateParams()) {
            $adminField->setData('data-validation-params', $this->getAdminDataValidateParams());
        }

        return $this;
    }

    /**
     * @return string
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

    /**
     * @return string
     */
    public function getAdminDataValidateParams()
    {
        return '';
    }
}
