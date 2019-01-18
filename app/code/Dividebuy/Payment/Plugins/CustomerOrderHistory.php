<?php
namespace Dividebuy\Payment\Plugins;

/**
 * Class CustomerOrderHistory
 */
class CustomerOrderHistory
{
    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function afterGetOrders(\Magento\Sales\Block\Order\History $order, \Magento\Sales\Model\ResourceModel\Order\Collection $collection)
    {
        return $collection->addAttributeToFilter("hide_dividebuy", ["eq" => 0]);
    }
}
