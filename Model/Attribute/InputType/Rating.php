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
class Rating extends AbstractInputType
{
    protected $defaultBackendType = 'int';

    protected $inputFieldType = 'note';

    protected $canDisplayInGrid = false;

    protected $backendModel = 'Alekseon\AlekseonEav\Model\Attribute\Backend\Rating';

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
     * @return false
     */
    public function canBeRequired()
    {
        return false;
    }
}
