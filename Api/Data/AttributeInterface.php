<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Api\Data;

interface AttributeInterface
{
    const ATTRIBUTE_CODE_MAX_LENGTH = 30;

    public function getAttributeId();
}
