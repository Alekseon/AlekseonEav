<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Backend;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Datetime
 * @package Alekseon\AlekseonEav\Model\Attribute\Backend
 */
class Datetime extends AbstractBackend
{
    /**
     * @param $object
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        try {
            $value = $this->formatDate($object->getData($attrCode));
        } catch (\Exception $e) {
            throw new LocalizedException(__('Invalid date'));
        }
        $object->setData($attrCode, $value);
        return parent::beforeSave($object);
    }

    /**
     * @param $date
     * @return null|string
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        // unix timestamp given - simply instantiate date object
        if (is_scalar($date) && preg_match('/^[0-9]+$/', $date)) {
            $date = (new \DateTime())->setTimestamp($date);
        } elseif (!($date instanceof \DateTimeInterface)) {
            // normalized format expecting Y-m-d[ H:i:s]  - time is optional
            $date = new \DateTime($date);
        }
        return $date->format('Y-m-d H:i:s');
    }
}
