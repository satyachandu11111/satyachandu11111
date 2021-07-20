<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Mageplaza\Webhook\Adapter\CreditMemo as SpbCreditMemo;
use Mageplaza\Webhook\Adapter\Adjustment;

class CreditMemoFactory
{
    /**
     * @var adjustmentCmFactory
     */
    private $adjustmentCmFactory;

	public function __construct(CreditMemoItemFactory $creditMemoItemFactory, CreditMemoCommentFactory $creditMemoCommentFactory, AdjustmentCmFactory $adjustmentCmFactory)
    {
        $this->creditMemoItemFactory = $creditMemoItemFactory;
        $this->creditMemoCommentFactory = $creditMemoCommentFactory;
        $this->adjustmentCmFactory = $adjustmentCmFactory;
    }

    /**
     * @param  $creditMemo
     */
    public function createFromCreditMemo(CreditmemoInterface $creditMemo): SpbCreditMemo
    {
        $spbCreditMemo = new SpbCreditMemo();
        $spbCreditMemo->setEntityId($creditMemo->getEntityId());
        $spbCreditMemo->setOrderId($creditMemo->getOrderId());
        $spbCreditMemo->setSubtotal($creditMemo->getGrandTotal());
        $spbCreditMemo->setState($creditMemo->getState());
        $spbCreditMemo->setCreatedAt($creditMemo->getCreatedAt());
        $spbCreditMemo->setUpdatedAt($creditMemo->getUpdatedAt());
        $spbCreditMemo->setShippingAmount($creditMemo->getShippingAmount());
        $spbCreditMemo->setAdjustment($creditMemo->getAdjustment());
        $items = [];
        $adjustmentAmount = [];

        foreach ($creditMemo->getItems() as $creditMemoItem){
            $items []= $this->creditMemoItemFactory->createFromCreditMemoItem($creditMemoItem);
        }

        $comments = [];
        foreach ($creditMemo->getComments() as $creditMemoComment){
            $comments []= $this->creditMemoCommentFactory->createFromCreditMemoComment($creditMemoComment);
        }

        if ($creditMemo->getDiscountAmount() && ($creditMemo->getDiscountAmount() > 0)) {
            $type = Adjustment::DISCOUNT;
            $adjustmentAmount[] = $this->adjustmentCmFactory->createFromAdjustment($creditMemo, $type, true, $creditMemo->getDiscountAmount());
        }

        if ($creditMemo->getTaxAmount() && ($creditMemo->getTaxAmount() > 0)) {
            $type = Adjustment::TAX;
            $adjustmentAmount[] = $this->adjustmentCmFactory->createFromAdjustment($creditMemo, $type, true, $creditMemo->getTaxAmount());
        }

        if ($creditMemo->getShippingAmount() && ($creditMemo->getShippingAmount() > 0)) {
            $type = Adjustment::SHIPPING;
            $adjustmentAmount[] = $this->adjustmentCmFactory->createFromAdjustment($creditMemo, $type, false, $creditMemo->getShippingAmount());
        }

        if ($creditMemo->getAdjustmentPositive() && ($creditMemo->getAdjustmentPositive() > 0)) {
            $type = Adjustment::ADJUSTMENTPOSITIVE;
            $adjustmentAmount[] = $this->adjustmentCmFactory->createFromAdjustment($creditMemo, $type, false, $creditMemo->getAdjustmentPositive());
        }

        if ($creditMemo->getAdjustmentNegative() && ($creditMemo->getAdjustmentNegative() > 0)) {
            $type = Adjustment::ADJUSTMENTNEGATIVE;
            $adjustmentAmount[] = $this->adjustmentCmFactory->createFromAdjustment($creditMemo, $type, true, $creditMemo->getAdjustmentNegative());
        }

        $spbCreditMemo->setItems($items);
        $spbCreditMemo->setComments($comments);
        $spbCreditMemo->setAdjustments($adjustmentAmount);
        return $spbCreditMemo;
    }
}