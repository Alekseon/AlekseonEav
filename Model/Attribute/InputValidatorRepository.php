<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute;

/**
 * Class InputValidatorRepository
 * @package Alekseon\AlekseonEav\Model\Attribute
 */
class InputValidatorRepository
{
    /**
     * @var array
     */
    protected $inputValidators;
    /**
     * @var array
     */
    protected $inputValidatorsByCodes;

    /**
     * InputValidatorRepository constructor.
     * @param array $inputValidators
     */
    public function __construct(
        array $inputValidators = []
    ) {
        $this->inputValidators = $inputValidators;
    }

    /**
     * @return array|null
     */
    public function getInputValidators()
    {
        if ($this->inputValidatorsByCodes == null) {
            $this->inputValidatorsByCodes = [];
            foreach ($this->inputValidators as $code => $data) {
                $validator = new \Magento\Framework\DataObject($data);
                $validator->setCode($code);
                $this->inputValidatorsByCodes[$code] = $validator;
            }
        }
        return $this->inputValidatorsByCodes;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getAttributeValidator($attribute)
    {
        $validators = $this->getInputValidators();
        $inputValidator = $attribute->getData('input_validator');
        if (isset($validators[$inputValidator])) {
            $validator = $validators[$inputValidator];
            if (!$validator->getModel()) {
                $model = $validator->getFactory()->create();
                $model->setData($validator->getData());
                $model->setAttribute($attribute);
                return $model;
            }

        }

        return false;
    }
}
