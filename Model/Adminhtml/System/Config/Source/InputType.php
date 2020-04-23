<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

use Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository;

/**
 * Class InputType
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
class InputType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var InputTypeRepository
     */
    private $inputTypeRepository;
    /**
     * @var
     */
    private $options;

    /**
     * InputType constructor.
     * @param InputTypeRepository $inputTypeRepository
     */
    public function __construct(
        InputTypeRepository $inputTypeRepository
    )
    {
        $this->inputTypeRepository = $inputTypeRepository;
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
            $this->options = [];
            $inputTypes = $this->inputTypeRepository->getFrontendInputTypes();
            foreach ($inputTypes as $inputType) {
                $this->options[$inputType->getCode()] = __($inputType->getLabel());
            }
        }
        return $this->options;
    }
}
