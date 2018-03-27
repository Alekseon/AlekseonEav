<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType;

/**
 * Class BackendTypeFactory
 * @package Alekseon\AlekseonEav\Model\Attribute
 */
class InputTypeRepository
{
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\TextFactory
     */
    private $textInputTypeFactory;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\TextareaFactory
     */
    private $textareaInputTypeFactory;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\BooleanFactory
     */
    private $booleanInputTypeFactory;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\SelectFactory
     */
    private $selectInputTypeFactory;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\MultiselectFactory
     */
    private $multiselectInputTypeFactory;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\DateFactory
     */
    private $dateInputTypeFactory;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\InputType\ImageFactory
     */
    private $imageInputTypeFactory;

    /**
     * InputTypeRepository constructor.
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\TextFactory $textInputTypeFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\TextareaFactory $textareaInputTypeFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\BooleanFactory $booleanInputTypeFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\SelectFactory $selectInputTypeFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\MultiselectFactory $multiselectInputTypeFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\DateFactory $dateInputTypeFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\InputType\ImageFactory $imageInputTypeFactory
     */
    public function __construct(
        \Alekseon\AlekseonEav\Model\Attribute\InputType\TextFactory $textInputTypeFactory,
        \Alekseon\AlekseonEav\Model\Attribute\InputType\TextareaFactory $textareaInputTypeFactory,
        \Alekseon\AlekseonEav\Model\Attribute\InputType\BooleanFactory $booleanInputTypeFactory,
        \Alekseon\AlekseonEav\Model\Attribute\InputType\SelectFactory $selectInputTypeFactory,
        \Alekseon\AlekseonEav\Model\Attribute\InputType\MultiselectFactory $multiselectInputTypeFactory,
        \Alekseon\AlekseonEav\Model\Attribute\InputType\DateFactory $dateInputTypeFactory,
        \Alekseon\AlekseonEav\Model\Attribute\InputType\ImageFactory $imageInputTypeFactory
    ) {
        $this->textareaInputTypeFactory = $textareaInputTypeFactory;
        $this->textInputTypeFactory = $textInputTypeFactory;
        $this->booleanInputTypeFactory = $booleanInputTypeFactory;
        $this->selectInputTypeFactory = $selectInputTypeFactory;
        $this->multiselectInputTypeFactory = $multiselectInputTypeFactory;
        $this->dateInputTypeFactory = $dateInputTypeFactory;
        $this->imageInputTypeFactory = $imageInputTypeFactory;
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
        switch ($frontendInput) {
            case InputType::INPUT_TYPE_TEXTAREA:
                $inputTypeModel = $this->textareaInputTypeFactory->create();
                break;
            case InputType::INPUT_TYPE_BOOLEAN:
                $inputTypeModel = $this->booleanInputTypeFactory->create();
                break;
            case InputType::INPUT_TYPE_SELECT:
                $inputTypeModel = $this->selectInputTypeFactory->create();
                break;
            case InputType::INPUT_TYPE_MULTISELECT:
                $inputTypeModel = $this->multiselectInputTypeFactory->create();
                break;
            case InputType::INPUT_TYPE_DATE:
                $inputTypeModel = $this->dateInputTypeFactory->create();
                break;
            case InputType::INPUT_TYPE_IMAGE:
                $inputTypeModel = $this->imageInputTypeFactory->create();
                break;
            case InputType::INPUT_TYPE_TEXT:
            default:
                $inputTypeModel = $this->textInputTypeFactory->create();
        }
        return $inputTypeModel;
    }
}
