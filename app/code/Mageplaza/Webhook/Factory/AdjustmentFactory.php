<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\OrderInterface;
use Mageplaza\Webhook\Adapter\Adjustment;

class AdjustmentFactory
{
	public function createFromAdjustment(OrderInterface $order, $type=null, $amountType=true, $amount)
    {
    	if ($amountType == true) {
    		$refund = '+';
    	} else {
    		$refund = '-';
    	}
        $adjustment = new Adjustment();
        $adjustment->setOrderId($order->getId());
        $adjustment->setType($type);
        $adjustment->setAmount((int) $refund.$amount);
        return $adjustment;
    }
}
