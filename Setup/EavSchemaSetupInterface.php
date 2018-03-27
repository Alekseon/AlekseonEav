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
interface EavSchemaSetupInterface
{
    /**
     * @param $attributeTableName
     * @param $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * @param string $entityTableIdField
     * @return mixed
     */
    public function createFullEavStructure(
        $attributeTableName,
        $eavEntityTablesPrefix,
        $entitiesToCreate = null,
        $entityTableName = null,
        $entityTableIdField = 'entity_id'
    );

    /**
     * @param $attributeTableName
     * @return mixed
     */
    public function createEavAttributeTable($attributeTableName);

    /**
     * @param $attributeTableName
     * @param $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * @param null $entityTableIdField
     * @return mixed
     */
    public function createEavEntitiesTables(
        $attributeTableName,
        $eavEntityTablesPrefix,
        $entitiesToCreate = null,
        $entityTableName = null,
        $entityTableIdField = null
    );

    /**
     * @param $attributeTableName
     * @param $optionsTableName
     * @return mixed
     */
    public function createOptionTables($attributeTableName, $optionsTableName);
}
