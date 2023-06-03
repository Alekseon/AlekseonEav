<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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

    /**
     * @param string $backendModelCode
     * @param $backendModel
     * @return mixed
     */
    public function addBackendModel(string $backendModelCode, $backendModel);

    /**
     * @return string
     */
    public function getDefaultValue();

    /**
     * @return string|null
     */
    public function getAttributeCode();
}
