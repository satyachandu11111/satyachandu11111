<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Mageplaza\Webhook\Adapter\CreditMemoItem as SpbCreditMemoItem;
use Magento\Framework\UrlInterface;
use Mageplaza\Webhook\Adapter\Adjustment;

class CreditMemoItemFactory
{
    /**
     * @var adjustmentCmItemFactory
     */
    private $adjustmentCmItemFactory;


    public function __construct(AdjustmentCmItemFactory $adjustmentCmItemFactory)
    {
        $this->adjustmentCmItemFactory = $adjustmentCmItemFactory;
    }

	public function createFromCreditMemoItem(CreditmemoItemInterface $creditmemoItem): SpbCreditMemoItem
    {
        $spbCreditMemoItem = new SpbCreditMemoItem();
        $spbCreditMemoItem->setEntityId($creditmemoItem->getEntityId());
        $spbCreditMemoItem->setOrderItemId($creditmemoItem->getOrderItemId());
        $spbCreditMemoItem->setPrice($creditmemoItem->getPrice());
        $spbCreditMemoItem->setQty($creditmemoItem->getQtyOrdered());
        $spbCreditMemoItem->setRowTotal($creditmemoItem->getRowTotal());
        $spbCreditMemoItem->setTaxAmount($creditmemoItem->getTaxAmount());
        $spbCreditMemoItem->setDiscountAmount($creditmemoItem->getDiscountAmount());
        $adjustmentAmount = [];

        if ($creditmemoItem->getDiscountAmount() && ($creditmemoItem->getDiscountAmount() > 0)) {
            $type = Adjustment::DISCOUNT;
            $adjustmentAmount[] = $this->adjustmentCmItemFactory->createFromAdjustmentItem($creditmemoItem, $type,true, $creditmemoItem->getDiscountAmount());
        }

        if ($creditmemoItem->getTaxAmount() && ($creditmemoItem->getTaxAmount() > 0)) {
            $type = Adjustment::TAX;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($creditmemoItem, $type,true, $creditmemoItem->getTaxAmount());
        }

        if ($creditmemoItem->getWeeeTaxAppliedAmount() && ($creditmemoItem->getWeeeTaxAppliedAmount() > 0)) {
            $type = Adjustment::WEEE_TAX;
            $adjustmentAmount[] = $this->adjustmentItemFactory->createFromAdjustmentItem($creditmemoItem, $type,true, $creditmemoItem->getWeeeTaxAppliedAmount());
        }
        
        $spbCreditMemoItem->setAdjustments($adjustmentAmount);
        
        return $spbCreditMemoItem;
    }
}