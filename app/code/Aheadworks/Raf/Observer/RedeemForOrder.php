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
 * Class RedeemForOrder
 *
 * @package Aheadworks\Raf\Observer
 */
class RedeemForOrder implements ObserverInterface
{
    /**
     *  {@inheritDoc}
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var $order \Magento\Sales\Model\Order **/
        $order = $event->getOrder();
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $event->getQuote();

        if ($quote->getAwRafAmount()) {
            $order->setAwRafAmount($quote->getAwRafAmount());
            $order->setBaseAwRafAmount($quote->getBaseAwRafAmount());
            $order->setAwRafIsFriendDiscount($quote->getAwRafIsFriendDiscount());
            $order->setAwRafReferralLink($quote->getAwRafReferralLink());
            $order->setAwRafPercentAmount($quote->getAwRafPercentAmount());
            $order->setAwRafAmountType($quote->getAwRafAmountType());

            $order->setAwRafShippingPercent(
                $order->getExtensionAttributes()->getAwRafShippingPercent()
            );
            $order->setAwRafShippingAmount(
                $order->getExtensionAttributes()->getAwRafShippingAmount()
            );
            $order->setBaseAwRafShippingAmount(
                $order->getExtensionAttributes()->getBaseAwRafShippingAmount()
            );
        }
    }
}
