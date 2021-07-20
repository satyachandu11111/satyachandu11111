<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\OrderItemInterface;
use Mageplaza\Webhook\Adapter\OrderItem as SpbOrderItem;
use Magento\Framework\UrlInterface;
use Mageplaza\Webhook\Adapter\Adjustment;

class OrderItemFactory
{
    /**
     * @var adjustmentItemFactory
     */
    private $adjustmentItemFactory;


    public function __construct(AdjustmentItemFactory $adjustmentItemFactory)
    {
        $this->adjustmentItemFactory = $adjustmentItemFactory;
    }

    public function createFromOrderItem(OrderItemInterface $orderItem): SpbOrderItem
    {
        $spbOrderItem = new SpbOrderItem();
        $spbOrderItem->setSku($orderItem->getSku());
        $spbOrderItem->setChannelItemId($orderItem->getParentItem() ?
            $orderItem->getParentItem()->getItemId(): $orderItem->getItemId());
        $spbOrderItem->setChannelProductId($orderItem->getProductId());
        $spbOrderItem->setTitle($orderItem->getName());
        $spbOrderItem->setUnitPrice($orderItem->getPrice()*100);
        $spbOrderItem->setQuantity($orderItem->getQtyOrdered());
        $spbOrderItem->setImageUrl($orderItem->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA).'catalog/product' .$orderItem->getProduct()->getImage());
//        $spbOrderItem->setTotal($orderItem->getRowTotal()*100);
//        $spbOrderItem->setTaxAmount($orderItem->getTaxAmount()*100);
//        $spbOrderItem->setDiscountAmount($orderItem->getDiscountAmount()*100);
        $adjustmentAmount = [];

        if ($orderItem->getTaxAmount() && ($orderItem->getTaxAmount() > 0)) {
            $type = Adjustment::TAX;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,true, $orderItem->getTaxAmount());
        }

        if ($orderItem->getGwPrice() && ($orderItem->getGwPrice() > 0)) {
            $type = Adjustment::GIFT_WRAPPER;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,true, $orderItem->getGwPrice());
        }

        if ($orderItem->getGwTaxAmount() && ($orderItem->getGwTaxAmount() > 0)) {
            $type = Adjustment::GIFT_WRAPPER_TAX;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,true, $orderItem->getGwTaxAmount());
        }

        if ($orderItem->getWeeeTaxAppliedAmount() && ($orderItem->getWeeeTaxAppliedAmount() > 0)) {
            $type = Adjustment::WEEE_TAX;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,true, $orderItem->getWeeeTaxAppliedAmount());
        }

        if ($orderItem->getTaxRefunded() && ($orderItem->getTaxRefunded() > 0)) {
            $type = Adjustment::Tax_REFUNDED;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,false, $orderItem->getTaxRefunded());
        }

        if ($orderItem->getGwPriceRefunded() && ($orderItem->getGwPriceRefunded() > 0)) {
            $type = Adjustment::GIFT_WRAPPER_REFUNDED;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,false, $orderItem->getTaxRefunded());
        }

        if ($orderItem->getDiscountAmount() && ($orderItem->getDiscountAmount() > 0)) {
            $type = Adjustment::DISCOUNT;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($orderItem, $type,false, $orderItem->getDiscountAmount());
        }

        $spbOrderItem->setAdjustments($adjustmentAmount);

        return $spbOrderItem;
    }

}
