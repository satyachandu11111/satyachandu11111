<?php
namespace Dividebuy\RetailerConfig\Plugins;

/**
 * Class DashboardGrapphOrderReports
 */
class DashboardGraphOrderReports
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
    public function afterPrepareSummary(\Magento\Sales\Model\ResourceModel\Order\Collection $order, $range, $customStart, $customEnd, $isFilter = 0)
    {
        return $order->addFieldToFilter('hide_dividebuy',0);
    }
}
