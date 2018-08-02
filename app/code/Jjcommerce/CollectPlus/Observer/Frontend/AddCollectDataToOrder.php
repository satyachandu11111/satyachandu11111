<?php

/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */


namespace Jjcommerce\CollectPlus\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

class AddCollectDataToOrder implements ObserverInterface
{

    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();

        $pos = strpos($order->getShippingMethod(), 'collect_collect');

        if ($pos !== false) {
            $order->setSmsAlert($quote->getSmsAlert());

            $order->setAgentData($quote->getAgentData());
        } else {
            $order->setSmsAlert('');
            $order->setAgentData('');
            $quote->setSmsAlert('');
            $quote->setAgentData('');
        }

    }
}
