<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Setup\Operation;

use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;

class AddImageAlt
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(OptionSettingRepositoryInterface::TABLE),
            'small_image_alt',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '255',
                'nullable' => false,
                'default' => '',
                'comment' => 'Image alt'
            ]
        );
    }
}
