<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addColumnsToAttributeTable($setup, 'alekseon_eav_attribute');
        }

        $setup->endSetup();
    }

    /**
     * @param $setup
     * @param $attributeTableName
     */
    protected function addColumnsToAttributeTable($setup, $attributeTableName)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'is_user_defined',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is User Defined'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'default_value',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '64k',
                'comment' => 'Default Value'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'is_unique',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is Unique'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'frontend_class',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'comment' => 'Frontend Class'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'is_wysiwyg_enabled',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is WYSIWYG Enabled'
            ]
        );
    }
}
