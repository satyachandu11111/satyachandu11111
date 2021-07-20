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

use Aheadworks\Raf\Api\Data\TotalsInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Payment\Model\Cart;

/**
 * Class AddPaymentRafCardItem
 *
 * @package Aheadworks\Raf\Observer
 */
class AddPaymentRafCardItem implements ObserverInterface
{
    /**
     * Merge RAF amount into discount of payment checkout totals
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $value = abs($salesEntity->getDataUsingMethod(TotalsInterface::BASE_AW_RAF_AMOUNT));
        if ($value > 0.0001) {
            $cart->addDiscount((double)$value);
        }
    }
}
