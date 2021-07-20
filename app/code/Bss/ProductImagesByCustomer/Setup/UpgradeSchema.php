<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Bss\ProductImagesByCustomer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package Amasty\Shopby\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
                
             $this->addColumnsCustomerName($installer);

        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
                
             $this->addColumnsCustomerMessage($installer);

        }

        if (version_compare($context->getVersion(), '1.0.10', '<')) {
                
            $this->addColumnsCustomerDate($installer);

        }

        $installer->endSetup();
    }


    protected function addColumnsCustomerName($installer)
    {
        $customerImageTable  = $installer->getTable('bss_images_customer_upload');
        

        $columnName = 'customer_name';
        $definition = [
            'type' => Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'Customer Name',
        ];

        $connection = $installer->getConnection();

        if ($installer->getConnection()->isTableExists($customerImageTable)) {

            $connection->addColumn($customerImageTable, $columnName, $definition);
        }
   
    }

    protected function addColumnsCustomerMessage($installer)
    {
        $customerImageTable  = $installer->getTable('bss_images_customer_upload');
        

        $columnName = 'customer_message';
        $definition = [
            'type' => Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'Customer Message',
        ];

        $connection = $installer->getConnection();

        if ($installer->getConnection()->isTableExists($customerImageTable)) {

            $connection->addColumn($customerImageTable, $columnName, $definition);
        }
   
    }


    protected function addColumnsCustomerDate($installer)
    {
        $customerImageTable  = $installer->getTable('bss_images_customer_upload');

        $columnName = 'customer_date';
        $definition = [
            'type' => Table::TYPE_DATE,
            'nullable' => false,
            'comment' => 'Customer Date',
        ];

        $connection = $installer->getConnection();

        if ($installer->getConnection()->isTableExists($customerImageTable)) {
            $connection->addColumn($customerImageTable, $columnName, $definition);
        }
    }

    
}
