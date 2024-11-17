<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

use Magento\Framework\Data\Form\Element\AbstractElement;
use \Alekseon\AlekseonEav\Block\Adminhtml\Entity\Edit\Form;

/**
 * Class AbstractValidator
 * @package Alekseon\AlekseonEav\Model\Attribute\InputValidator
 */
class Email extends AbstractValidator
{
    /**
     * @var string
     */
    protected $code = 'email';

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
     * @inheritDoc
     */
    public function prepareAdminField(AbstractElement $adminField)
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
