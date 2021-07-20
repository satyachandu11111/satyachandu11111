<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\CreditmemoInterface;
use Mageplaza\Webhook\Adapter\Adjustment;

class AdjustmentCmFactory
{
	public function createFromAdjustment(CreditmemoInterface $creditMemo, $type=null, $amountType=true, $amount)
    {
    	if ($amountType == true) {
    		$refund = '+';
    	} else {
    		$refund = '-';
    	}
        $adjustment = new Adjustment();
        $adjustment->setOrderId($creditMemo->getId());
        $adjustment->setType($type);
        $adjustment->setAmount($refund.$amount);
        return $adjustment;
    }
}