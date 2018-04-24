<?php
namespace Homescapes\Completelook\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Table update
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableName = $setup->getTable('completelook_product');
        $version = $context->getVersion();
        $connection = $setup->getConnection();
        
        if (version_compare($version, '1.1.0') < 0) 
        {
            //Custom table
           $connection->addColumn(
                $setup->getTable($tableName),
                'position',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'comment' =>'position',
                    'unsigned'  => true,
                    'nullable' => false,
                ]

            );
        }

        $setup->endSetup();
    }
}