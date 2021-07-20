<?php

namespace HubBox\HubBox\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {

            $installer->getConnection()->addColumn(
                $installer->getTable('hubbox_quote'),
                'collect_point_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                    'default' => 'hubbox',
                    'comment' => 'Collect Point Type (private/hubbox)'
                ]
            );
        }

        $installer->endSetup();
    }
}