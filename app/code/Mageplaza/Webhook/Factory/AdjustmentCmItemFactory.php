<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Mageplaza\Webhook\Adapter\Adjustment;

class AdjustmentCmItemFactory
{
	public function createFromAdjustmentItem(CreditmemoItemInterface $creditMemoItem, $type=null, $amountType=true, $amount)
    {
    	if ($amountType == true) {
    		$refund = '+';
    	} else {
    		$refund = '-';
    	}
        $adjustment = new Adjustment();
        $adjustment->setOrderItemId($creditMemoItem->getItemId());
        $adjustment->setType($type);
        $adjustment->setAmount((int)$refund.$amount);
        return $adjustment;
    }
}
