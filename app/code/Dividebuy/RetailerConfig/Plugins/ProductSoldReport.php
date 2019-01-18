<?php
namespace Dividebuy\RetailerConfig\Plugins;

/**
 * Class ProductSoldReport
 */
class ProductSoldReport
{
    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function afterSetStoreIds(\Magento\Reports\Model\ResourceModel\Product\Sold\Collection $product, $storeIds)
    {
        return $product->getSelect()->where("order.hide_dividebuy = ?", 0);
    }
}
