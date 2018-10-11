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
                $this->frontendInputTypesByCodes[$code] = $data;
            }
        }
        return $this->frontendInputTypesByCodes;
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
            $inputTypeModel = $inputTypes[$frontendInput]['factory']->create();
        } else {
            if ($frontendInput != self::DEFAULT_INPUT_TYPE_CODE) {
                $inputTypeModel = $this->getInputTypeModelByFrontendInput(self::DEFAULT_INPUT_TYPE_CODE); // return text as default
            }
        }

        return $inputTypeModel;
    }
}
