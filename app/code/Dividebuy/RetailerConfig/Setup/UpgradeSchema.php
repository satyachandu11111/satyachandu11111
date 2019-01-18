<?php

namespace Dividebuy\RetailerConfig\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getTable('sales_order_grid');

        $columns = [
            'hide_dividebuy' => [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'default' => '0',
                'comment' => 'Hide DivideBuy Orders',
            ],

        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($table, $name, $definition);
        }

        $installer->endSetup();

    }
}
