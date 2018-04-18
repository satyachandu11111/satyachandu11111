<?php
namespace Homescapes\Completelook\Setup;

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

        if (!$installer->tableExists('completelook_product')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('completelook_product'))
                ->addColumn(
                    'completelook_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true]
                )
                ->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null, ['nullable' => false,'unsigned' => true])
                ->addColumn('look_product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false,'unsigned' => true])                
                ->addIndex(
                $installer->getIdxName('completelook_product', ['completelook_id']),
                ['completelook_id']
                )->addIndex(
                    $installer->getIdxName('completelook_product', ['product_id']),
                    ['product_id']
                )
                ->addForeignKey(
                $installer->getFkName('completelook_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('completelook_product', 'look_product_id', 'catalog_product_entity', 'entity_id'),
                'look_product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Complete Look Products');

            $installer->getConnection()->createTable($table);
        }


        $installer->endSetup();
    }
}

