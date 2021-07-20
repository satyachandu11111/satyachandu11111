<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\Total\Creditmemo;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Class Advocate
 *
 * @package Aheadworks\Raf\Model\Total\Creditmemo
 */
class Raf extends AbstractTotal
{
    /**
     * {@inheritdoc}
     */
    public function collect(Creditmemo $creditmemo)
    {
        $creditmemo->setAwRafAmount(0);
        $creditmemo->setBaseAwRafAmount(0);

        $order = $creditmemo->getOrder();
        if ($order->getBaseAwRafAmount() && $order->getBaseAwRafInvoiced() != 0) {
            list($totalAmount, $baseTotalAmount) = $this->calculateTotalAmount($order, $creditmemo);
            /** @var $item \Magento\Sales\Model\Order\Creditmemo\Item */
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemAmount = (double)$orderItem->getAwRafInvoiced();
                $baseOrderItemAmount = (double)$orderItem->getBaseAwRafInvoiced();
                $orderItemQty = $orderItem->getQtyInvoiced();

                if ($orderItemAmount && $orderItemQty) {
                    // Resolve rounding problems
                    $amount = $orderItemAmount - $orderItem->getAwRafRefunded();
                    $baseAmount = $baseOrderItemAmount - $orderItem->getBaseAwRafRefunded();
                    if (!$item->isLast()) {
                        $activeQty = $orderItemQty - $orderItem->getQtyRefunded();
                        $amount = $creditmemo->roundPrice(
                            $amount / $activeQty * $item->getQty(),
                            'regular',
                            true
                        );
                        $baseAmount = $creditmemo->roundPrice(
                            $baseAmount / $activeQty * $item->getQty(),
                            'base',
                            true
                        );
                    }

                    $item->setAwRafAmount($amount);
                    $item->setBaseAwRafAmount($baseAmount);

                    $totalAmount += $amount;
                    $baseTotalAmount += $baseAmount;
                }
            }

            if ($baseTotalAmount > 0) {
                $creditmemo->setBaseAwRafAmount(-$baseTotalAmount);
                $creditmemo->setAwRafAmount(-$totalAmount);

                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalAmount);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalAmount);
            }
        }

        if ($creditmemo->getGrandTotal() <= 0) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        return $this;
    }

    /**
     * Calculate total amount
     *
     * @param Order $order
     * @param Creditmemo $creditmemo
     * @return array
     */
    private function calculateTotalAmount($order, $creditmemo)
    {
        $totalAmount = 0;
        $baseTotalAmount = 0;

        // Calculate how much shipping discount should be applied basing on how much shipping should be refunded
        $creditmemoBaseShippingAmount = (float)$creditmemo->getBaseShippingAmount();
        if ($creditmemoBaseShippingAmount) {
            $baseShippingDiscount = $creditmemoBaseShippingAmount
                * ($order->getBaseAwRafShippingAmount()
                    + $order->getBaseShippingDiscountAmount()
                    + $order->getBaseAwRewardPointsShippingAmount()
                )
                / $order->getBaseShippingAmount();
            $shippingDiscount = $order->getShippingAmount() * $baseShippingDiscount / $order->getBaseShippingAmount();

            $totalAmount += $shippingDiscount;
            $baseTotalAmount += $baseShippingDiscount;
        }

        return [$totalAmount, $baseTotalAmount];
    }
}
