<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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
