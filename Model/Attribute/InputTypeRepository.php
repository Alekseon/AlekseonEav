<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute;

/**
 * Class BackendTypeFactory
 * @package Alekseon\AlekseonEav\Model\Attribute
 */
class InputTypeRepository
{
    const DEFAULT_INPUT_TYPE_CODE = 'text';
    /**
     * @var array
     */
    private $frontendInputTypes;
    /**
     * @var
     */
    private $frontendInputTypesByCodes;

    /**
     * InputTypeRepository constructor.
     * @param array $frontendInputTypes
     */
    public function __construct(
        array $frontendInputTypes = []
    ) {
        $this->frontendInputTypes = $frontendInputTypes;
    }

    /**
     * @return array|null
     */
    public function getFrontendInputTypes()
    {
        if ($this->frontendInputTypesByCodes === null) {
            $this->frontendInputTypesByCodes = [];
            foreach ($this->frontendInputTypes as $code => $data) {
                $inputType = new \Magento\Framework\DataObject($data);
                $inputType->setCode($code);
                $this->frontendInputTypesByCodes[$code] = $inputType;
            }
        }
        return $this->frontendInputTypesByCodes;
    }

    /**
     * @return bool|mixed
     */
    public function getFrontendInputTypeConfigByAttribute($attribute)
    {
        $inputTypes = $this->getFrontendInputTypes();
        $frontendInput = $attribute->getFrontendInput();
        if (isset($inputTypes[$frontendInput])) {
            return $inputTypes[$frontendInput];
        } else {
            return false;
        }
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getInputTypeModelByAttribute($attribute)
    {
        $frontendInput = $attribute->getFrontendInput();
        $inputTypeModel = $this->getInputTypeModelByFrontendInput($frontendInput);
        $inputTypeModel->setAttribute($attribute);
        return $inputTypeModel;
    }

    /**
     * @param $frontendInput
     * @return mixed
     */
    public function getInputTypeModelByFrontendInput($frontendInput)
    {
        $inputTypes = $this->getFrontendInputTypes();
        if (isset($inputTypes[$frontendInput])) {
            $frontendInputType = $inputTypes[$frontendInput];
            $inputTypeModel = $frontendInputType->getFactory()->create();
        } else {
            if ($frontendInput != self::DEFAULT_INPUT_TYPE_CODE) {
                $inputTypeModel = $this->getInputTypeModelByFrontendInput(self::DEFAULT_INPUT_TYPE_CODE); // return text as default
            }
        }

        return $inputTypeModel;
    }
}
