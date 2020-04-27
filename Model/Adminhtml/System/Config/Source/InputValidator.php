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
class InputValidator implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Alekseon\AlekseonEav\Model\InputValidatorRepository
     */
    protected $inputValidatorRepository;
    /**
     * @var
     */
    private $options;

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
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $options = $this->getOptionArray();
        foreach ($options as $optionCode => $optionLabel) {
            $optionArray[] = [
                'value' => $optionCode,
                'label' => $optionLabel,
            ];
        }
        return $optionArray;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        if ($this->options === null) {
            $this->options = [0 => __('None')];
            $inputValidators = $this->inputValidatorRepository->getInputValidators();
            foreach ($inputValidators as $inputValidator) {
                $this->options[$inputValidator->getCode()] = __($inputValidator->getLabel());
            }
        }
        return $this->options;
    }
}
