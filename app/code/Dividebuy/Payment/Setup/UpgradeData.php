<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dividebuy\Payment\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $statusTable = $setup->getTable('sales_order_status');
        $statusStateTable = $setup->getTable('sales_order_status_state');

        $setup->getConnection()->query("INSERT INTO ".$statusTable."(status, label) VALUES('dividebuy_pending', 'Dividebuy Pending')");

        $setup->getConnection()->query("INSERT INTO ".$statusStateTable."(status, state, is_default) VALUES('dividebuy_pending', '".\Magento\Sales\Model\Order::STATE_NEW."', 0)");

        $setup->endSetup();
    }
}
