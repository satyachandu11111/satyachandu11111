<?php

namespace Homescapes\EmailVerificationApi\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Db\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	/**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$setup->startSetup();
    	$table = $setup->getConnection()->newTable(
    		$setup->getTable('email_verification')
    	)->addColumn(
    			'customer_id',
    			Table::TYPE_INTEGER,
    			null,
    			['identity' => true, 'nullable' => false, 'primary' => true],
    			'Customer ID'
    		)->addColumn(
    			'email',
    			Table::TYPE_TEXT,
    			255,
    			['nullable' => false],
    			'Customer Email'
    		)->addColumn(
                'verification_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer Verification Code'
            )->addColumn(
    			'created_at',
    			Table::TYPE_TIMESTAMP,
    			null,
    			['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
    			'TIME CREATED'
    		)->addColumn(
    			'updated_at',
    			Table::TYPE_TIMESTAMP,
    			null,
    			['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
    			'TIME FOR UPDATE'
    		)->setComment(
    			'Customer Email Verification table'
    		);
    	

    	$setup->getConnection()->createTable($table);

    	$setup->endSetup();
    }
}