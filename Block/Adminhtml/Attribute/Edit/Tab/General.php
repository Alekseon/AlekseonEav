<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit\Tab;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\DefaultValueProvider;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputValidator;
use Magento\Backend\Block\Widget\Form\Generic;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use \Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository;

/**
 * Class General
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit
 */
class General extends Generic
{

    /**
     * @return mixed
     */
    protected $attribute;
    /**
     * @var InputType
     */
    protected $inputTypeSource;
    /**
     * @var Scopes
     */
    protected $scopesSource;
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNoSource;
    /**
     * @var InputTypeRepository
     */
    protected $inputTypeRepository;
    /**
     * @var InputValidator
     */
    protected $inputValidatorSource;
    /**
     * @var DefaultValueProvider
     */
    protected $defaultValueProviderSource;

    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNoSource
     * @param InputTypeRepository $inputTypeRepository
     * @param InputType $inputTypeSource
     * @param InputValidator $inputValidatorSource
     * @param DefaultValueProvider $defaultValueProviderSource
     * @param Scopes $scopesSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNoSource,
        InputTypeRepository $inputTypeRepository,
        InputType $inputTypeSource,
        InputValidator $inputValidatorSource,
        DefaultValueProvider $defaultValueProviderSource,
        Scopes $scopesSource,
        array $data = []
    ) {
        $this->inputTypeSource = $inputTypeSource;
        $this->yesNoSource = $yesNoSource;
        $this->scopesSource = $scopesSource;
        $this->inputTypeRepository = $inputTypeRepository;
        $this->inputValidatorSource = $inputValidatorSource;
        $this->defaultValueProviderSource = $defaultValueProviderSource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return mixed
     */
    public function getAttributeObject()
    {
        if (null === $this->attribute) {
            return $this->_coreRegistry->registry('current_attribute');
        }
        return $this->attribute;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() // @codingStandardsIgnoreLine
    {
        $attributeObject = $this->getAttributeObject();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Attribute Properties')]);

        if ($attributeObject->getAttributeId()) {
            $baseFieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $baseFieldset->addField(
            'frontend_label',
            'text',
            [
                'name' => 'frontend_label',
                'label' => __('Frontend Label'),
                'title' => __('Frontend label'),
                'required' => true,
            ]
        );

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Alekseon\AlekseonEav\Model\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );

        $baseFieldset->addField(
            'attribute_code',
            'text',
            [
                'name' => 'attribute_code',
                'label' => __('Attribute Code'),
                'title' => __('Attribute Code'),
                'note' => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    \Alekseon\AlekseonEav\Model\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
                'required' => true
            ]
        );

        if ($attributeObject->getCanUseGroup()) {
            $baseFieldset->addField(
                'group_code',
                'text',
                [
                    'name' => 'group_code',
                    'label' => __('Group Code'),
                    'title' => __('Group Code'),
                    'note' => __('It can be used to separate attributes for specfic groups in forms.'),
                    'disabled' => !$attributeObject->getIsGroupEditable(),
                ]
            );
        }

        $baseFieldset->addField(
            'frontend_input',
            'select',
            [
                'name' => 'frontend_input',
                'label' => __('Input Type for Store Owner'),
                'title' => __('Input Type for Store Owner'),
                'value' => InputTypeRepository::DEFAULT_INPUT_TYPE_CODE,
                'values' => $this->inputTypeSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'scope',
            'select',
            [
                'name' => 'scope',
                'label' => __('Scope'),
                'title' => __('Scope'),
                'value' => SCOPES::SCOPE_GLOBAL,
                'values' => $this->scopesSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'is_required',
            'select',
            [
                'name' => 'is_required',
                'label' => __('Is Required'),
                'title' => __('Is Required'),
                'values' => $this->yesNoSource->toOptionArray()
            ]
        );

        $baseFieldset->addField(
            'visible_in_grid',
            'select',
            [
                'name' => 'visible_in_grid',
                'label' => __('Visible In Grid'),
                'title' => __('Visible In Grid'),
                'values' => $this->yesNoSource->toOptionArray()
            ]
        );

        if (!$attributeObject->getId() || $attributeObject->getInputTypeModel()->canUseWysiwyg()) {
            $baseFieldset->addField(
                'is_wysiwyg_enabled',
                'select',
                [
                    'name' => 'is_wysiwyg_enabled',
                    'label' => __('Enable Wysiwyg'),
                    'title' => __('Enable Wysiwyg'),
                    'values' => $this->yesNoSource->toOptionArray()
                ]
            );
        }

        $this->inputValidatorSource->setAttribute($attributeObject);
        if ($attributeObject->getId()) {
            $showInputValidatorSelect = $this->inputValidatorSource->hasOptions();
        } else {
            $showInputValidatorSelect = true;
        }

        if ($showInputValidatorSelect) {
            $baseFieldset->addField(
                'input_validator',
                'select',
                [
                    'name' => 'input_validator',
                    'label' => __('Input Validator'),
                    'title' => __('Input Validator'),
                    'values' => $this->inputValidatorSource->toOptionArray()
                ]
            );
        }

        if ($attributeObject->getId()) {
            $this->defaultValueProviderSource->setAttribute($attributeObject);

            if ($this->defaultValueProviderSource->hasOptions()) {
                $baseFieldset->addField(
                    'default_value',
                    'select',
                    [
                        'name' => 'default_value',
                        'label' => __('Default Value'),
                        'title' => __('Default Value'),
                        'values' => $this->defaultValueProviderSource->toOptionArray()
                    ]
                );
            }
        }

        if (!$attributeObject->getId() || $attributeObject->getInputTypeModel()->hasOptionCodes()) {
            $baseFieldset->addField(
                'has_option_codes',
                'select',
                [
                    'name' => 'has_option_codes',
                    'label' => __('Option Codes'),
                    'title' => __('Option Codes'),
                    'values' => $this->yesNoSource->toOptionArray()
                ]
            );
        }

        $baseFieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'value' => '0',
            ]
        );

        $baseFieldset->addField(
            'note',
            'text',
            [
                'name' => 'note',
                'label' => __('Note'),
                'title' => __('Note'),
            ]
        );

        if (!$attributeObject->isAttributeCodeEditable()) {
            $form->getElement('attribute_code')->setDisabled(true);
        }

        if ($attributeObject->getId()) {
            $form->getElement('frontend_input')->setDisabled(true);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return $this
     */
    protected function _initFormValues() // @codingStandardsIgnoreLine
    {
        $values = $this->getAttributeObject()->getData();

        $defaultValues = explode(',', $this->getAttributeObject()->getData('default_value') ?? '');
        // if default value provider is set then its always as first element for atribute select types
        if (isset($defaultValues[0])) {
            $values['default_value'] = $defaultValues[0];
        }

        $this->getForm()->addValues($values);
        return parent::_initFormValues();
    }
}
