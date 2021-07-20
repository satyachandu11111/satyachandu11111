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
namespace Aheadworks\Raf\Model\Total\Invoice;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Class Raf
 *
 * @package Aheadworks\Raf\Model\Total\Invoice
 */
class Raf extends AbstractTotal
{
    /**
     * {@inheritDoc}
     */
    public function collect(Invoice $invoice)
    {
        $invoice->setAwRafAmount(0);
        $invoice->setBaseAwRafAmount(0);

        $order = $invoice->getOrder();
        if ($order->getBaseAwRafAmount()
            && $order->getBaseAwRafInvoiced() != $order->getBaseAwRafAmount()
        ) {
            list($totalAmount, $baseTotalAmount) = $this->calculateTotalAmount($order);
            /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemAmount = (double)$orderItem->getAwRafAmount();
                $baseOrderItemAmount = (double)$orderItem->getBaseAwRafAmount();
                $orderItemQty = $orderItem->getQtyOrdered();

                if ($orderItemAmount && $orderItemQty) {
                    // Resolve rounding problems
                    $amount = $orderItemAmount - $orderItem->getAwRafInvoiced();
                    $baseAmount = $baseOrderItemAmount - $orderItem->getBaseAwRafInvoiced();
                    if (!$item->isLast()) {
                        $activeQty = $orderItemQty - $orderItem->getQtyInvoiced();
                        $amount = $invoice->roundPrice(
                            $amount / $activeQty * $item->getQty(),
                            'regular',
                            true
                        );
                        $baseAmount = $invoice->roundPrice(
                            $baseAmount / $activeQty * $item->getQty(),
                            'base',
                            true
                        );
                    }

                    $item->setAwRafAmount($amount);
                    $item->setBaseAwRafAmount($baseAmount);

                    $orderItem->setAwRafInvoiced(
                        $orderItem->getAwRafInvoiced() + $item->getAwRafAmount()
                    );
                    $orderItem->setBaseAwRafInvoiced(
                        $orderItem->getBaseAwRafInvoiced() + $item->getBaseAwRafAmount()
                    );

                    $totalAmount += $amount;
                    $baseTotalAmount += $baseAmount;
                }
            }

            if ($baseTotalAmount > 0) {
                $invoice->setBaseAwRafAmount(-$baseTotalAmount);
                $invoice->setAwRafAmount(-$totalAmount);
            }

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalAmount);
        }
        return $this;
    }

    /**
     * Calculate total amount
     *
     * @param Order $order
     * @return array
     */
    private function calculateTotalAmount($order)
    {
        $totalAmount = 0;
        $baseTotalAmount = 0;

        // Checking if RAF shipping amount was added in previous invoices
        $addRafShippingAmount = true;
        foreach ($order->getInvoiceCollection() as $previousInvoice) {
            if ($previousInvoice->getAwRafAmount()) {
                $addRafShippingAmount = false;
            }
        }

        if ($addRafShippingAmount) {
            $totalAmount = $totalAmount + $order->getAwRafShippingAmount();
            $baseTotalAmount = $baseTotalAmount + $order->getBaseAwRafShippingAmount();
        }

        return [$totalAmount, $baseTotalAmount];
    }
}
