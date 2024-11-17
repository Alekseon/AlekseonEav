<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

use Alekseon\AlekseonEav\Model\Attribute\Source\AbstractSource;

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
     * @var string
     */
    protected $validator = 'Alekseon\AlekseonEav\Model\Attribute\InputValidator\Select';

    /**
     * Select constructor.
     * @param \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory
     */
    public function __construct(
        \Alekseon\AlekseonEav\Model\Attribute\Source\OptionFactory $optionFactory
    ) {
        $this->optionFactory = $optionFactory;
        parent::__construct();
    }

    /**
     * @return bool|void
     */
    public function hasOptionCodes()
    {
        /**
         * get sourcel model, because option codes depends if there is source model
         */
        $this->getSourceModel();
        return parent::hasOptionCodes();
    }

    /**
     * @return AbstractSource
     */
    public function getSourceModel()
    {
        if ($this->sourceModel === null) {
            if ($sourceModel = $this->getAttribute()->getData('source_model')) {
                try {
                    $this->sourceModel = $this->createObject($sourceModel);
                } catch (\Exception $e) {
                    $this->logger->error(
                        "AlekseonEav: unable to load for attribute '{$this->getAttribute()->getAttributeCode()}': "
                        . $e->getMessage()
                    );
                }
            }

            if ($this->sourceModel === null) {
                $this->hasOptionCodes = true;
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
            $fieldConfig['options'] = [];
            if ($this->hasEmptyOption()) {
                $fieldConfig['options'] = ['' => __('Not Selected')];
            }
            $fieldConfig['options'] += $this->getSourceModel()->getOptionArray();
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

    /**
     * @param $value
     * @param null $storeId
     * @return bool|mixed
     */
    public function getValueAsText($value, $storeId = null)
    {
        $sourceModel = $this->getSourceModel();
        $sourceModel->setStoreId($storeId);
        $options = $sourceModel->getOptionArray();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return false;
    }

    /**
     * @param $optionId
     * @return bool|void
     */
    public function getOptionCode($optionId)
    {
        if (!$this->hasOptionCodes()) {
            return false;
        }

        return $this->getSourceModel()->getOptionCode($optionId);
    }

    /**
     * @return string|array
     */
    public function getDefaultValue()
    {
        $defaultValue = (string) parent::getDefaultValue();
        if ($defaultValue !== '') {
            return explode(',', $defaultValue);
        }

        return '';
    }

    /**
     * @return bool
     */
    public function hasEmptyOption()
    {
        $sourceModel = $this->getSourceModel();
        $options = $sourceModel->getOptions();

        if (isset($options[0])) {
            return false;
        }

        return true;
    }

    /**
     * @return true
     */
    public function hasCustomOptionSource()
    {
        return true;
    }
}
