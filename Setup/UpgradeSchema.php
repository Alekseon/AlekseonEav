<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addColumnsToAttributeTable($setup, 'alekseon_eav_attribute');
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.2', '<')) {
            $eavSetup->createFrontendLabelsTable('alekseon_eav_attribute', 'alekseon_eav_attribute_frontend_label');
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {
            $eavSetup->installOptionCodes('alekseon_eav_attribute', 'alekseon_eav_attribute_option');
        }

        // version": "101.0.0",
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->updateToVersion_101($setup, 'alekseon_eav_attribute');
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->updateAttributeCodeColumnSize($setup, 'alekseon_eav_attribute');
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

    /**
     * @param $setup
     * @param $attributeTableName
     */
    public function updateToVersion_101($setup, $attributeTableName)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'group_code',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'comment' => 'Attributes Group Code'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'input_validator',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'comment' => 'Attributes Input Validator'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'attribute_extra_params',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '64k',
                'comment' => 'Attribute Extra Params'
            ]
        );

        $setup->getConnection()->dropColumn($attributeTableName, 'has_option_code');

        $setup->getConnection()->addColumn(
            $setup->getTable($attributeTableName),
            'has_option_codes',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Has Option Codes'
            ]
        );
    }

    /**
     * @param $setup
     */
    public function updateAttributeCodeColumnSize($setup, $attributeTableName)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable($attributeTableName),
            'attribute_code',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'length' => 255,
                'comment' => 'Attribute Code'
            ]
        );
    }
}
