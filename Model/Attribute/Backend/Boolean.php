<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

class Boolean extends AbstractBackend
{
    /**
     * @param $object
     * @return Boolean
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = (int) $object->getData($attrCode);
        if ($value != \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean::VALUE_YES) {
            $value = \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean::VALUE_NO;
        }
        $object->setData($attrCode, $value);
        return parent::beforeSave($object);
    }
}
