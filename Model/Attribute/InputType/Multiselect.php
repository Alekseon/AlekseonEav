<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

/**
 * Class Multiselect
 * @package Alekseon\AlekseonEav\Model\Attribute\InputType
 */
class Multiselect extends Select
{
    /**
     * @var string
     */
    protected $inputFieldType = 'multiselect';
    /**
     * @var bool
     */
    protected $hasFieldOptions = false;
    /**
     * @var bool
     */
    protected $hasFieldValues = true;
    /**
     * @var string
     */
    protected $defaultBackendType = 'varchar';
    /**
     * @var bool
     */
    protected $backendModel = 'Alekseon\AlekseonEav\Model\Attribute\Backend\ArrayBackend';
    /**
     * @var string
     */
    protected $validator = 'Alekseon\AlekseonEav\Model\Attribute\InputValidator\Multiselect';

    /**
     * @param $fieldConfig
     * @return $this
     */
    public function prepareFormFieldConfig(&$fieldConfig)
    {
        if ($this->usesSource()) {
            $fieldConfig['values'] = $this->getSourceModel()->getAllOptions();
        }
        return $this;
    }

    /**
     * @param $columnConfig
     * @return Multiselect|void
     */
    public function prepareGridColumnConfig(&$columnConfig)
    {
        $columnConfig['sortable'] = false;
        $columnConfig['filter'] = false;
        $columnConfig['renderer'] = \Alekseon\AlekseonEav\Block\Adminhtml\Entity\Grid\Renderer\Multiselect::class;
        return $this;
    }

    /**
     * @param $value
     * @param null $storeId
     * @return bool|mixed
     */
    public function getValueAsText($value, $storeId = null)
    {
        return false;
    }
}
