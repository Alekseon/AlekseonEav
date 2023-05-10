<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Alekseon\AlekseonEav\Setup\EavSchemaSetupFactory;

/**
 *
 */
class CreateEavTables implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;
    /**
     * @var EavSchemaSetupFactory
     */
    private $eavSetupFactory;

    /**
     * EnableSegmentation constructor.
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup,
        EavSchemaSetupFactory $eavSetupFactory
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return CreateEavTables|void
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $setup = $this->schemaSetup;

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->createFullEavStructure('alekseon_eav_attribute', 'alekseon_eav_entity');

        // below lines do the same as "createFullEavStructure" method
        //$eavSetup->createEavAttributeTable('alekseon_eav_attribute');
        //$eavSetup->createOptionTables('alekseon_eav_attribute', 'alekseon_eav_attribute_option');
        //$eavSetup->createEavEntitiesTables('alekseon_eav_attribute', 'alekseon_eav_entity');
        //$eavSetup->createFrontendLabelsTable('alekseon_eav_attribute', 'alekseon_eav_attribute_frontend_label');

        // updates for old module versions:
        $this->updateAttributeTableV2('alekseon_eav_attribute');
        $this->updateAttributeTableV3('alekseon_eav_attribute');
        $this->updateAttributeTableV4('alekseon_eav_attribute');

        $this->schemaSetup->endSetup();
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
