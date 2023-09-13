<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class AbstractSource
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
abstract class AbstractSource
{
    /**
     * @var
     */
    private $optionsArray = [];
    /**
     * @var
     */
    private $attribute;
    /**
     * @var
     */
    private $storeId = null;
    /**
     * @var bool
     */
    protected $validateOptionKeyOnEntitySave = true;

    /**
     * @return mixed
     */
    abstract public function getOptions();

    /**
     * @param $storeId
     * @return mixed
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @return null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @return array
     */
    public function getAllOptions($withEmpty = false)
    {
        $allOptions = [];
        if ($withEmpty) {
            $allOptions[] = ['value' => '', 'label' => $this->getEmptyOptionLabel()];
        }
        $options = $this->getOptionArray();
        foreach ($options as $value => $label) {
            $allOptions[] = ['value' => $value, 'label' => $label];
        }

        return $allOptions;
    }

    /**
     * @return mixed
     */
    public function getOptionArray()
    {
        $storeId = $this->storeId ?? 0;
        if (!array_key_exists($storeId, $this->optionsArray)) {
            $this->optionsArray[$storeId] = $this->getOptions();
        }
        return $this->optionsArray[$storeId];
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

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    protected function getEmptyOptionLabel()
    {
        return '';
    }

    /**
     * @param $optionId
     * @return array | bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getStoreLabels($optionId)
    {
        return false;
    }

    /**
     * @return string | bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOptionCode($optionId)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function validateOptionKeyOnObjectSave()
    {
        return $this->validateOptionKeyOnEntitySave;
    }
}
