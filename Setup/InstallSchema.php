<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Alekseon\AlekseonEav\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var EavSchemaSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallSchema constructor.
     * @param EavSchemaSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSchemaSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $installer = $setup;
        $installer->startSetup();

        $eavSetup->createFullEavStructure('alekseon_eav_attribute', 'alekseon_eav_entity');

        // below lines do the same as "createFullEavStructure" method
        //$eavSetup->createEavAttributeTable('alekseon_eav_attribute');
        //$eavSetup->createOptionTables('alekseon_eav_attribute', 'alekseon_eav_attribute_option');
        //$eavSetup->createEavEntitiesTables('alekseon_eav_attribute', 'alekseon_eav_entity');
        //$eavSetup->createFrontendLabelsTable('alekseon_eav_attribute', 'alekseon_eav_attribute_frontend_label');

        $installer->endSetup();
    }
}
