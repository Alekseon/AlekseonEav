<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

use Magento\Store\Model\Store;

class Boolean extends AbstractBackend
{
    /**
     * @param $object
     * @return Boolean
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();

        if ($object->getData($attrCode) === null && $object->getStoreId() != Store::DEFAULT_STORE_ID) {
            return parent::beforeSave($object);
        }

        $value = (int) $object->getData($attrCode);
        if ($value != \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean::VALUE_YES) {
            $value = \Alekseon\AlekseonEav\Model\Attribute\Source\Boolean::VALUE_NO;
        }
        $object->setData($attrCode, $value);
        return parent::beforeSave($object);
    }
}
