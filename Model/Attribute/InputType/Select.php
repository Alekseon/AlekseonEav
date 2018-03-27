<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

/**
 * Class Select
 * @package Alekseon\AlekseonEav\Model\Attribute\InputType
 */
class Select extends AbstractInputType
{
    /**
     * @var
     */
    private $sourceModel;
    /**
     * @var \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory
     */
    private $optionFactory;
    /**
     * @var string
     */
    protected $defaultBackendType = 'int';
    /**
     * @var string
     */
    protected $inputFieldType = 'select';
    /**
     * @var string
     */
    protected $gridColumnType = 'options';
    /**
     * @var bool
     */
    protected $hasFieldOptions = true;
    /**
     * @var bool
     */
    protected $usesSource = true;

    /**
     * Select constructor.
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory
     */
    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory
    ) {
        $this->optionFactory = $optionFactory;
        parent::__construct($universalFactory);
    }

    /**
     * @return bool
     */
    public function usesSource()
    {
        if (!$this->getSourceModel()) {
            return false;
        }
        return parent::usesSource();
    }

    /**
     * @return \Magento\Framework\Validator\Builder
     */
    public function getSourceModel()
    {
        if ($this->sourceModel === null) {
            if ($sourceModel = $this->getAttribute()->getData('source_model')) {
                $this->sourceModel = $this->createObject($sourceModel);
            } else {
                $this->canManageOptions = true;
                $this->sourceModel = $this->optionFactory->create();
            }
            $this->sourceModel->setAttribute($this->getAttribute());
        }
        return $this->sourceModel;
    }

    /**
     * @param $fieldConfig
     * @return $this
     */
    public function prepareFormFieldConfig(&$fieldConfig)
    {
        if ($this->usesSource()) {
            $fieldConfig['options'] = $this->getSourceModel()->getOptionArray();
        }
        return $this;
    }

    /**
     * @param $columnConfig
     * @return $this
     */
    public function prepareGridColumnConfig(&$columnConfig)
    {
        if ($this->usesSource()) {
            $columnConfig['options'] = $this->getSourceModel()->getOptionArray();
        }
        return $this;
    }
}
