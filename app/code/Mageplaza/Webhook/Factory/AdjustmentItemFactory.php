<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\OrderItemInterface;
use Mageplaza\Webhook\Adapter\Adjustment;

class AdjustmentItemFactory
{
	public function createFromAdjustmentItem(OrderItemInterface $orderItem, $type=null, $amountType=true, $amount)
    {
    	if ($amountType == true) {
    		$refund = '+';
    	} else {
    		$refund = '-';
    	}
        $adjustment = new Adjustment();
        $adjustment->setOrderItemId($orderItem->getItemId());
        $adjustment->setType($type);
        $adjustment->setAmount((int)$refund.$amount);
        return $adjustment;
    }
}
