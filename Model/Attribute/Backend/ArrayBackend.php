<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

/**
 * Class ArrayBackend
 * @package Alekseon\AlekseonEav\Model\Attribute\Backend
 */
class ArrayBackend extends AbstractBackend
{
    /**
     * @param $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (is_array($value)) {
            $object->setData($attrCode, implode(',', $value));
        }
        return parent::beforeSave($object);
    }

    /**
     * @param $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!is_array($value)) {
            $object->setData($attrCode, explode(',', (string) $value));
        }
        return parent::afterLoad($object);
    }
}
