<?php
namespace Dividebuy\RetailerConfig\Plugins;

/**
 * Class CustomerOrdersReport
 */
class CustomerOrdersReport
{
    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function afterSetStoreIds(\Magento\Reports\Model\ResourceModel\Customer\Orders\Collection $collection, $storeIds)
    {
        return $collection->addAttributeToFilter("hide_dividebuy", ["eq" => 0]);
    }
}
