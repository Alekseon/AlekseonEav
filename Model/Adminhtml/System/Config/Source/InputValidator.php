<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
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
            $this->options = [0 => __('None')];
            $inputValidators = $this->inputValidatorRepository->getInputValidators();
            foreach ($inputValidators as $inputValidator) {
                $this->options[$inputValidator->getCode()] = __($inputValidator->getLabel());
                $this->hasOptions = true;
            }
        }
        return $this->options;
    }
}
