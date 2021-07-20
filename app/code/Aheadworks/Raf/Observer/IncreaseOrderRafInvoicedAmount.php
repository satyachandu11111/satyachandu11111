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
namespace Aheadworks\Raf\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class IncreaseOrderRafInvoicedAmount
 *
 * @package Aheadworks\Raf\Observer
 */
class IncreaseOrderRafInvoicedAmount implements ObserverInterface
{
    /**
     * Increase order aw_raf_invoiced attribute based on created invoice
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getBaseAwRafAmount()) {
            $order->setBaseAwRafInvoiced(
                $order->getBaseAwRafInvoiced() + $invoice->getBaseAwRafAmount()
            );
            $order->setAwRafInvoiced(
                $order->getAwRafInvoiced() + $invoice->getAwRafAmount()
            );
        }
        return $this;
    }
}
