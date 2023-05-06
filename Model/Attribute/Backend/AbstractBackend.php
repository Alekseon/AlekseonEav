<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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
    private $attribute;

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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDelete($object)
    {
        return $this;
    }

    /**
     * @param $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete($object)
    {
        return $this;
    }
}
