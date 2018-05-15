<?php
namespace Homescapes\Newsletters\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            $tableName = $setup->getTable('newsletter_subscriber');

            if ($setup->getConnection()->isTableExists($tableName) == true) {

                $setup->getConnection()->addColumn($tableName, 'subscriber_date', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'unsigned' => true,
                'nullable' => true,
                'default' => Null,
                'comment' => 'Subscriber Date'
                ]);

                $setup->getConnection()->changeColumn(
                    $tableName,
                    'c_firstname',
                    'subscriber_firstname',
                    ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => ''],
                    'Subscriber Firstname'
                );

                $setup->getConnection()->changeColumn(
                    $tableName,
                    'c_lastname',
                    'subscriber_lastname',
                    ['type' => Table::TYPE_TEXT, 'nullable' => true, 'default' => ''],
                    'Subscriber Lastname'
                );

            }
            
        }
        
        $setup->endSetup();
    }
}