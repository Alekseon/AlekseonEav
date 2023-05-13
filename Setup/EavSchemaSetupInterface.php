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
     * @return mixed
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
     * @return mixed
     */
    public function createEavAttributeTable(string $attributeTableName);

    /**
     * @param string $attributeTableName
     * @param string $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * @param null $entityTableIdField
     * @return mixed
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
     * @return mixed
     */
    public function createOptionTables(string $attributeTableName, string $optionsTableName);
}
