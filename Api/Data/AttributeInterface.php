<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Api\Data;

/**
 * Interface AttributeInterface
 * @package Alekseon\AlekseonEav\Api\Data
 */
interface AttributeInterface
{
    const ATTRIBUTE_CODE_MAX_LENGTH = 255;

    /**
     * @return mixed
     */
    public function getAttributeId();
}
