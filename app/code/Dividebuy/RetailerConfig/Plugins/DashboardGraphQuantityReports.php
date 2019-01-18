<?php
namespace Dividebuy\RetailerConfig\Plugins;

/**
 * Class DashboardGraphQuantityReports
 */
class DashboardGraphQuantityReports
{
    /**
     * Prepare report summary
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return $this
     */
    public function afterCreate(\Magento\Reports\Model\ResourceModel\Order\CollectionFactory $orderCollection, \Magento\Sales\Model\ResourceModel\Order\Collection $order, $customerId = null)
    {
        return $order->addFieldToFilter('hide_dividebuy',0);
    }
}
