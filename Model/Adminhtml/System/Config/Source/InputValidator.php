<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

/**
 * Class Validator
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
class InputValidator extends AbstractSource
{
    /**
     * @var \Alekseon\AlekseonEav\Model\InputValidatorRepository
     */
    protected $inputValidatorRepository;
    /**
     * @var
     */
    protected $inputTypeValidators;

    /**
     * InputValidator constructor.
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputValidatorRepository $inputValidatorRepository
     */
    public function __construct(
        \Alekseon\AlekseonEav\Model\Attribute\InputValidatorRepository $inputValidatorRepository
    )
    {
        $this->inputValidatorRepository = $inputValidatorRepository;
    }

    /**
     * @return array
     */
    public function getOptionArray(): array
    {
        if ($this->options === null) {
            $validators = $this->getValidatorsByInputType($this->attribute->getFrontendInput());
            if (!empty($validators)) {
                $this->hasOptions = true;
            }
            $this->options = $validators;
        }
        return $this->options;
    }

    /**
     * @param $inputType
     */
    public function getValidatorsByInputType($inputType)
    {
        $inputValidators = $this->inputValidatorRepository->getInputValidators();
        if ($this->inputTypeValidators === null) {
            foreach ($inputValidators as $validator) {
                $applicapleFrontendInputs = $validator->getApplicableFrontendInputs();
                foreach ($applicapleFrontendInputs as $frontendInput) {
                    $this->inputTypeValidators[$frontendInput][$validator->getCode()] = __($validator->getLabel());
                }
            }
        }

        if (isset($this->inputTypeValidators[$inputType])) {
            $validators = $this->inputTypeValidators[$inputType];
            return array_merge([0 => __('None')], $validators);
        }

        return [];
    }
}
