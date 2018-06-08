<?php
namespace Homescapes\Preorder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

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
        
        $quote = 'quote';
        $orderTable = 'sales_order';
        $orderGridTable = 'sales_order_grid';
        
        //Quote table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quote),
                'preorder',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' =>'Pre Order',
                    'default' => '0'
                ]
            );
        //Order table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'preorder',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' =>'Pre Order',
                    'default' => '0'
                ]
            );
        //Order_grid table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderGridTable),
                'preorder',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' =>'Pre Order',
                    'default' => '0'
                ]
            );
        

        $setup->endSetup();
    }
}



