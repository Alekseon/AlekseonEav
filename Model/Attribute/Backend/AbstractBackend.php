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
abstract class AbstractBackend
{
    /**
     * @var
     */
    protected $attribute;

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
     * @param $object
     * @return $this
     */
    public function beforeSave($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    public function afterSave($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    public function afterLoad($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    public function beforeDelete($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    public function afterDelete($object)
    {
        return $this;
    }

    /**
     * @param $object
     */
    public function isAttributeValueUpdated($object, $isAttributeVAlueUpdated)
    {
        return $isAttributeVAlueUpdated;
    }
}
