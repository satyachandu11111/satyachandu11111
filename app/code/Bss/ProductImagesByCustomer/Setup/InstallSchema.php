<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductImagesByCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductImagesByCustomer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Create Database
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /**
         * Create table 'bss_product_images_by_customer'
         */
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('bss_images_customer_upload'))
            ->addColumn(
                'bss_image_customer_upload_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Id image customer upload'
            )
            ->addColumn(
                'approve',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Approve'
            )
            ->addColumn(
                'id_product',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Product of image'
            )
            ->addColumn(
                'link_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                500,
                ['nullable' => false, 'default' => ''],
                'Link Image'
            )
            ->addColumn(
                'email_customer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                500,
                ['nullable' => false, 'default' => ''],
                'Email Customer'
            )
            ->addColumn(
                'id_store',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Store View'
            )
            ->addIndex(
                $installer->getIdxName('bss_images_customer_upload', ['bss_image_customer_upload_id']),
                ['bss_image_customer_upload_id']
            )
            ->setComment("Images customer table");
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
