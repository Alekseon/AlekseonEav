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
abstract class AbstractInputType
{
    /**
     * @var string
     */
    protected $inputFieldType = 'text';
    /**
     * @var string
     */
    protected $defaultBackendType = 'varchar';
    /**
     * @var string
     */
    protected $gridColumnType = 'text';
    /**
     * @var bool
     */
    protected $usesSource = false;
    /**
     * @var bool
     */
    protected $canManageOptions = false;
    /**
     * @var bool
     */
    protected $hasFieldOptions = false;
    /**
     * @var bool
     */
    protected $hasFieldValues = false;
    /**
     * @var bool
     */
    protected $backendModel = false;
    /**
     * @var bool
     */
    protected $canDisplayInGrid = true;

    /**
     * @var
     */
    private $attribute;
    /**
     * @var \Magento\Framework\Validator\UniversalFactory
     */
    private $universalFactory;

    /**
     * AbstractInputType constructor.
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory
    ) {
        $this->universalFactory = $universalFactory;
    }

    /**
     * @param $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return bool
     */
    public function usesSource()
    {
        return $this->usesSource;
    }

    /**
     * @return string
     */
    public function getDefaultBackendType()
    {
        return $this->defaultBackendType;
    }

    /**
     * @return string
     */
    public function getInputFieldType()
    {
        return $this->inputFieldType;
    }

    /**
     * @return string
     */
    public function getGridColumnType()
    {
        return $this->gridColumnType;
    }

    public function getSourceModel()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function canManageOptions()
    {
        $this->getSourceModel();
        return $this->canManageOptions;
    }

    /**
     * @return mixed
     */
    public function hasFieldOptions()
    {
        return $this->hasFieldOptions;
    }

    /**
     * @return mixed
     */
    public function hasFieldValues()
    {
        return $this->hasFieldValues;
    }

    /**
     * @return bool|\Magento\Framework\Validator\Builder
     */
    public function getBackendModel()
    {
        if ($backendModel = $this->getAttribute()->getData('backend_model')) {
            return $this->createObject($backendModel);
        }
        if ($this->backendModel) {
            return $this->createObject($this->backendModel);
        }
        return false;
    }

    /**
     * @param $class
     * @return \Magento\Framework\Validator\Builder
     */
    protected function createObject($class)
    {
        return $this->universalFactory->create($class);
    }

    /**
     * @param $fieldConfig
     * @return $this
     */
    public function prepareFormFieldConfig(&$fieldConfig)
    {
        return $this;
    }

    /**
     * @param $columnConfig
     * @return $this
     */
    public function prepareGridColumnConfig(&$columnConfig)
    {
        return $this;
    }

    public function canDisplayInGrid()
    {
        return $this->canDisplayInGrid;
    }
}
