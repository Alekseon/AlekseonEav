<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

/**
 * Class AbstractBackendType
 * @package Alekseon\AlekseonEav\Model\Attribute\BackendType
 */
class Rating extends Select
{
    protected $defaultBackendType = 'int';
    protected $inputFieldType = 'note';
    protected $gridColumnType = 'options';
    protected $backendModel = 'Alekseon\AlekseonEav\Model\Attribute\Backend\Rating';

    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\Source\Rating
     */
    protected $ratingSource;

    /**
     * Boolean constructor.
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\Source\Rating $ratingSource
     */
    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory,
        \Alekseon\AlekseonEav\Model\Attribute\Source\Rating $ratingSource
    ) {
        $this->ratingSource = $ratingSource;
        parent::__construct($universalFactory, $optionFactory);
    }

    /**
     * @return bool
     */
    public function getSourceModel()
    {
        return $this->ratingSource;
    }

    /**
     * @param $fieldConfig
     * @return Rating|void
     */
    public function prepareFormFieldConfig(&$fieldConfig)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $value = $this->getForm()->getDataObject()->getData($attributeCode);

        $fieldConfig['text'] = $this->getForm()->getLayout()
            ->createBlock(\Alekseon\AlekseonEav\Block\Adminhtml\Entity\Edit\Form\Renderer\Rating::class)
            ->setInputName($attributeCode)
            ->setInputValue($value)
            ->toHtml();
    }

    /**
     * @param $columnConfig
     * @return $this|Rating
     */
    public function prepareGridColumnConfig(&$columnConfig)
    {
        $columnConfig['renderer'] = \Alekseon\AlekseonEav\Block\Adminhtml\Entity\Grid\Renderer\Rating::class;
        $columnConfig['options'] = $this->getSourceModel()->getOptionArray();
        return $this;
    }

    /**
     * @return false
     */
    public function isRequiredEditable()
    {
        return false;
    }
}
