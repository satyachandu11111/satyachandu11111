<?php
namespace Dividebuy\RetailerConfig\Plugins;

/**
 * Class CustomerTotalsReport
 */
class CustomerTotalsReport
{
    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function afterSetStoreIds(\Magento\Reports\Model\ResourceModel\Customer\Totals\Collection $collection, $storeIds)
    {
        return $collection->addAttributeToFilter("hide_dividebuy", ["eq" => 0]);
    }
}
