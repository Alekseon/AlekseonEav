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
interface EavSchemaSetupInterface
{
    /**
     * @param string $attributeTableName
     * @param string $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * @param string $entityTableIdField
     * @deprecated 101.2.16
     */
    public function createFullEavStructure(
        string $attributeTableName,
        string $eavEntityTablesPrefix,
        $entitiesToCreate = null,
        $entityTableName = null,
        $entityTableIdField = 'entity_id'
    );

    /**
     * @param string $attributeTableName
     * @deprecated 101.2.16
     */
    public function createEavAttributeTable(string $attributeTableName);

    /**
     * @param string $attributeTableName
     * @param string $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * @param null $entityTableIdField
     * @deprecated 101.2.16
     */
    public function createEavEntitiesTables(
        string $attributeTableName,
        string $eavEntityTablesPrefix,
        $entitiesToCreate = null,
        $entityTableName = null,
        $entityTableIdField = null
    );

    /**
     * @param string $attributeTableName
     * @param string $optionsTableName
     * @deprecated 101.2.16
     */
    public function createOptionTables(string $attributeTableName, string $optionsTableName);
}
