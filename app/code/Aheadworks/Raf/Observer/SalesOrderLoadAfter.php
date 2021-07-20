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
use Magento\Sales\Model\Order;

/**
 * Class SalesOrderLoadAfter
 *
 * @package Aheadworks\Raf\Observer
 */
class SalesOrderLoadAfter implements ObserverInterface
{
    /**
     * Set forced canCreditmemo flag
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() || $order->getState() === Order::STATE_CLOSED) {
            return $this;
        }

        if ((abs($order->getAwRafInvoiced()) - abs($order->getAwRafRefunded())) > 0) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }
}
