<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Block\Adminhtml\Entity\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Alekseon\AlekseonEav\Api\Data\EntityInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class General
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute\Edit
 */
abstract class Form extends Generic
{
    /**
     * @return mixed
     */
    abstract public function getDataObject();

    /**
     * @return $this
     */
    protected function _prepareForm() // @codingStandardsIgnoreLine
    {
        $this->getForm()->setDataObject($this->getDataObject());
        return parent::_prepareForm();
    }

    /**
     * @param $formFieldset
     * @param EntityInterface $entity
     * @return $this
     */
    public function addAllAttributeFields($formFieldset, EntityInterface $entity)
    {
        $resource = $entity->getResource();
        $resource->loadAllAttributes();
        $attributes = $resource->getAllLoadedAttributes();

        foreach ($attributes as $attribute) {
            $this->addAttributeField($formFieldset, $attribute);
        }
        return $this;
    }

    /**
     * @param $formFieldset
     * @param AttributeInterface $attribute
     */
    public function addAttributeField($formFieldset, AttributeInterface $attribute)
    {
        $inputTypeModel = $attribute->getInputTypeModel();
        $fieldType = $inputTypeModel->getInputFieldType();

        $fieldConfig = [
            'name' => $attribute->getAttributeCode(),
            'label' => $attribute->getDefaultFrontendLabel(),
            'title' => $attribute->getDefaultFrontendLabel(),
        ];

        if ($attribute->getIsRequired()) {
            $fieldConfig['required'] = true;
        }

        $inputTypeModel->prepareFormFieldConfig($fieldConfig);

        $element = $formFieldset->addField(
            $attribute->getAttributeCode(),
            $fieldType,
            $fieldConfig
        );

        $renderers = $this->_getAdditionalFormElementRenderers();
        if (isset($renderers[$attribute->getAttributeCode()])) {
            $element->setRenderer($renderers[$attribute->getAttributeCode()]);
        }

        $this->_addAdditionalFormElementData($element);

        $element->setEntityAttribute($attribute);
    }

    /**
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout() // @codingStandardsIgnoreLine
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Renderer\Element::class,
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Renderer\Fieldset::class,
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                \Alekseon\AlekseonEav\Block\Adminhtml\Entity\Edit\Form\Renderer\Fieldset\Element::class,
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    }

    /**
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return [];
    }

    /**
     * @param AbstractElement $element
     * @return $this
     */
    protected function _addAdditionalFormElementData(AbstractElement $element)
    {
        return $this;
    }
}
