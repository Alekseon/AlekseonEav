<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

/**
 * Class DefaultValue
 * @package Alekseon\AlekseonEav\Model\Attribute\Backend
 */
class DefaultValue extends AbstractBackend
{
    const MODE_NOT_SET = 'not_set';
    const MODE_SET_IF_EMPTY = 'set_if_empty';
    const MODE_FORCE_SET = 'force_set';

    protected $mode = self::MODE_NOT_SET;
    /**
     * @var
     */
    protected $defaultValue;

    /**
     * @param $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @param $object
     * @return DefaultValue|void
     */
    public function beforeSave($object)
    {
        if ($this->mode == self::MODE_NOT_SET) {
            return $this;
        }

        if ($this->defaultValue) {
            if ($this->mode === self::MODE_FORCE_SET || !$object->getData($this->getAttribute()->getAttributeCode())) {
                $object->setData($this->getAttribute()->getAttributeCode(), $this->defaultValue);
            }
        }

        return $this;
    }
}
