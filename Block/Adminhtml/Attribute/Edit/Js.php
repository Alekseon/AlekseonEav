<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputValidator;
use Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

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
     * @var JsonHexTag
     */
    private $jsonSerializer;

    /**
     * Js constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param InputTypeRepository $inputTypeRepository
     * @param InputValidator $validatorSource
     * @param JsonHexTag $jsonSerializer
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        InputTypeRepository $inputTypeRepository,
        InputValidator $validatorSource,
        JsonHexTag $jsonSerializer
    ) {
        $this->registry = $registry;
        $this->inputTypeRepository = $inputTypeRepository;
        $this->validatorSource = $validatorSource;
        $this->jsonSerializer = $jsonSerializer;
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
        $jsConfig = [];
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
     * Input types config encoded for use inside a "text/x-magento-init" block
     *
     * @return string
     */
    public function getSerializedJsConfig()
    {
        $jsConfig = $this->getJsConfig();
        if (!$jsConfig) {
            $jsConfig = $this->getDefaultJsConfig();
        }

        return $this->jsonSerializer->serialize($jsConfig);
    }

    /**
     * Kept for backward compatibility, used only when getJsConfig() returns nothing
     *
     * @return array
     */
    protected function getDefaultJsConfig()
    {
        return [
            'default' => [
                'show_options' => false,
                'can_be_visible_in_grid' => true,
                'can_use_wysiwyg' => false,
            ],
            'text' => [
                'can_use_input_validator' => true,
            ],
            'textarea' => [
                'can_use_wysiwyg' => true,
            ],
            'boolean' => [
                'optionInputType' => 'radio',
            ],
            'select' => [
                'show_options' => true,
                'optionInputType' => 'radio',
            ],
            'multiselect' => [
                'show_options' => true,
                'optionInputType' => 'checkbox',
            ],
            'date' => [],
            'image' => [],
        ];
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
