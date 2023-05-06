<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputValidator;
use Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository;

/**
 * Class Js
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit
 */
class Js extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var InputTypeRepository
     */
    protected $inputTypeRepository;
    /**
     * @var InputValidator
     */
    protected $validatorSource;

    /**
     * Js constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param InputTypeRepository $inputTypeRepository
     * @param InputValidator $validatorSource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        InputTypeRepository $inputTypeRepository,
        InputValidator $validatorSource
    ) {
        $this->registry = $registry;
        $this->inputTypeRepository = $inputTypeRepository;
        $this->validatorSource = $validatorSource;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->registry->registry('current_attribute');
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        $inputTypes = $this->inputTypeRepository->getFrontendInputTypes();
        foreach ($inputTypes as $inputType => $inputTypeConfig) {
            $inputModel = $this->inputTypeRepository->getInputTypeModelByFrontendInput($inputType);

            if (!isset($jsConfig[$inputTypeConfig->getCode()])) {
                $jsConfig[$inputTypeConfig->getCode()] = [];
            }

            $validatorOptions = $this->getValidatorOptions($inputType);

            $jsConfig[$inputTypeConfig->getCode()] = [
                'show_options' => $inputModel->usesSource(),
                'can_be_visible_in_grid' => $inputModel->canDisplayInGrid(),
                'can_use_wysiwyg' => $inputModel->canUseWysiwyg(),
                'validator_options' => $validatorOptions,
                'can_use_input_validator' => !empty($validatorOptions),
            ];
        }

        $jsConfig['boolean']['optionInputType'] = 'radio';
        $jsConfig['select']['optionInputType'] = 'radio';
        $jsConfig['multiselect']['optionInputType'] = 'checkbox';

        return $jsConfig;
    }

    /**
     * @return bool
     */
    public function canRefreshValidatorsList()
    {
        if ($this->getAttribute()->getId()) {
            return false;
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function getValidatorOptions($inputType)
    {
        return $this->validatorSource->getValidatorsByInputType($inputType);
    }
}
