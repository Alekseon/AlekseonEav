<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Setup;

/**
 * Interface SchemaSetupInterface
 * @package Alekseon\AlekseonEav\Setup
 */
interface EavDataSetupInterface
{
    public function createAttribute($attributeCode, $data, $exceptionIfAttributeAlreadyExists = false);

    public function updateAttribute($attributeCode, $data = [], $exceptionIfAttributeNotFound = false);

    public function createOrUpdateAttribute($attributeCode, $data = []);

    public function deleteAttribute($attributeCode);
}
