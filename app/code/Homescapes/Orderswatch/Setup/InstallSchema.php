<?php
namespace Homescapes\Orderswatch\Setup;

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
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('krish_orderswatch_sample')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('krish_orderswatch_sample'))
                ->addColumn(
                    'sample_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true]
                )
                ->addColumn('fname', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'First Name')
                ->addColumn('lname', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'Last Name')
                ->addColumn('email_address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'Email Id')
                ->addColumn('address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'Address')
                ->addColumn('city', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'City')
                ->addColumn('county', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'County')
                ->addColumn('product_sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'Product Sku')
                ->addColumn('country', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255, ['nullable' => false],'Country')
                ->addColumn('zip_code', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null,['nullable' => false],'Zip Code')
                ->addColumn('send_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,null, ['nullable' => false],'Sample Creation Time')
                ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,5, ['unsigned' => true,'nullable' => false],'Store ID')
                ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,255,['nullable' => false,'default' =>'Processing'] ,'Status')                     
                ->setComment('krish order swatch');
            $installer->getConnection()->createTable($table);
        }

        
         /*if (!$installer->tableExists('krish_orderswatch_sample_store')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('krish_orderswatch_sample_store'))
                ->addColumn(
                    'sample_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'nullable' => false, 'primary' => true],'Sample ID'
                )
                ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,5, ['unsigned' => true,'nullable' => false],'Store ID')
                ->addForeignKey(
                $installer->getFkName('krish_orderswatch_sample_store', 'sample_id', 'krish_orderswatch_sample', 'sample_id'),
                'sample_id',
                $installer->getTable('krish_orderswatch_sample'),
                'sample_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                $installer->getFkName('krish_orderswatch_sample_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )    
                ->setComment('order swatch To Store Linkage Table');
            $installer->getConnection()->createTable($table);
        } */
        
        

        $installer->endSetup();
    }
}


