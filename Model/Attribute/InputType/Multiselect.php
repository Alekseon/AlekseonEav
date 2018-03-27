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
    protected $canDisplayInGrid = false;
    /**
     * @var bool
     */
    protected $backendModel = 'Alekseon\AlekseonEav\Model\Attribute\Backend\ArrayBackend';

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
}
