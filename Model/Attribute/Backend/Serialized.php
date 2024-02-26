<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

/**
 * Class Serialized
 * @package Alekseon\AlekseonEav\Model\Attribute\Backend
 */
class Serialized extends AbstractBackend
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param $object
     * @return Serialized
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($value) {
            $value = $this->jsonSerializer->serialize($value);
            $object->setData($attrCode, $value);
        }
        return parent::beforeSave($object);
    }

    /**
     * @param $object
     * @return Serialized
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($value && !is_array($value)) {
            $value = $this->jsonSerializer->unserialize($value);
            $object->setData($attrCode, $value);
        }
        return parent::afterLoad($object);
    }
}
