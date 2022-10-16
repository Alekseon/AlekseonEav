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
     * @var
     */
    protected $backendModel;
    /**
     * @var
     */
    protected $backendModelMode = '';

    /**
     * @param Attribute $attribute
     * @return bool
     */
    public function canBeUsedForAttribute(Attribute $attribute)
    {
        $applicatbleInputTypes =  $this->getApplicableFrontendInputs() ?? [];
        if (!in_array($attribute->getFrontendInput(), $applicatbleInputTypes)) {
           return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        if ($this->getValue()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function getBackendModel()
    {
        if ($this->backendModel === null) {
            $backendModelFactory = $this->_data['default_value_backend_model_factory'] ?? false;
            if ($this->hasValue()) {
                $backendModel = $backendModelFactory->create();
                $backendModel->setMode($this->backendModelMode);
                $backendModel->setDefaultValue($this->getValue());
                $this->backendModel = $backendModel;
            }
        }
        return $this->backendModel;
    }

    /**
     * @return mixed
     */
    abstract public function getValue();
}
