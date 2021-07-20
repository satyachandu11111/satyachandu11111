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
use Aheadworks\Raf\Model\Config;
use Magento\Framework\Event\Observer;

/**
 * Class Refund
 *
 * @package Aheadworks\Raf\Observer
 */
class Refund implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Set refund amount to creditmemo
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        // If refund RAF amount
        if ($creditmemo->getBaseAwRafAmount()) {
            $order->setBaseAwRafRefunded($order->getBaseAwRafRefunded() + $creditmemo->getBaseAwRafAmount());
            $order->setAwRafRefunded($order->getAwRafRefunded() + $creditmemo->getAwRafAmount());

            /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItem->setAwRafRefunded($orderItem->getAwRafRefunded() + $item->getAwRafAmount());
                $orderItem->setBaseAwRafRefunded($orderItem->getBaseAwRafRefunded() + $item->getBaseAwRafAmount());
            }

            // we need to update flag after credit memo was refunded and order's properties changed
            if ($order->getAwRafInvoiced() < 0
                && $order->getAwRafInvoiced() == $order->getAwRafRefunded()
            ) {
                $order->setForcedCanCreditmemo(false);
            }
        }

        return $this;
    }
}
