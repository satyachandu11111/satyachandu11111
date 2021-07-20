<?php

namespace Homescapes\EmailVerificationApi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Db\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

	/**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$setup->startSetup();

    	if(version_compare($context->getVersion(), '1.0.1', '<')) {

    		$setup->getConnection()->addColumn(
    			$setup->getTable('email_verification'),
    			'status',
    			['nullable' => false, 'type' => Table::TYPE_BOOLEAN, 'comment' => 'Status']
    		);
    	}

        if(version_compare($context->getVersion(), '1.0.2', '<')) {

            $setup->getConnection()->addIndex(
                $setup->getTable('email_verification'),
                $setup->getIdxName('email_verification', ['email']),
                ['email']
            );

            $setup->getConnection()->addIndex(
                $setup->getTable('email_verification'),
                $setup->getIdxName('email_verification', ['status']),
                ['status']
            );

            $setup->getConnection()->addIndex(
                $setup->getTable('email_verification'),
                $setup->getIdxName('email_verification', ['verification_code']),
                ['verification_code']
            );
        }

        if(version_compare($context->getVersion(), '1.0.3', '<')) {

            /*$setup->getConnection()->addColumn(
                $setup->getTable('email_verification'),
                'store_id',
                ['nullable' => false, 'type' => Table::TYPE_SMALLINT, 'comment' => 'Store Id']
            );*/

            $connection = $setup->getConnection();
            $tablename = $setup->getTable('email_verification');

            $connection->addColumn(
                $setup->getTable($tablename),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 5,
                    'comment' =>'Store Id',
                    'unsigned'  => true,
                    'nullable' => false,
                ]

            );
            $connection->addIndex(
                $setup->getTable('email_verification'),
                $setup->getIdxName($setup->getTable('email_verification'),['store_id']),
                ['store_id']
            );
            $connection->addForeignKey(
                $setup->getFkName($tablename,'store_id',$setup->getTable('store'),'store_id'),
                $tablename,
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            );

        }

    	$setup->endSetup();
    }
}