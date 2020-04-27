<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
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
    protected $options;
    /**
     * @var
     */
    protected $attribute;

    /**
     * @return mixed
     */
    abstract public function getOptions();

    /**
     * @return array
     */
    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->options)) {
            $this->options = [];
            if ($withEmpty) {
                $this->options[] = ['value' => '', 'label' => $this->getEmptyOptionLabel()];
            }
            $options = $this->getOptions();
            foreach ($options as $value => $label) {
                $this->options[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getOptionArray()
    {
        return $this->getOptions();
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
     * @return bool
     */
    public function getStoreLabels($optionId)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getOptionCode($optionId)
    {
        return false;
    }
}
