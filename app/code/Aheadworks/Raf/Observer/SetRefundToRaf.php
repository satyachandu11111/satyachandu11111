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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Event\Observer;

/**
 * Class SetRefundToRaf
 *
 * @package Aheadworks\Raf\Observer
 */
class SetRefundToRaf implements ObserverInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param RequestInterface $request
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        RequestInterface $request
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->request = $request;
    }

    /**
     * Set refund flag to creditmemo based on user input
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if ($this->request->getActionName() == 'updateQty') {
            return $this;
        }

        $input = $observer->getEvent()->getInput();
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $order = $observer->getEvent()->getCreditmemo()->getOrder();

        $enable = empty($order->getAwRafIsFriendDiscount())
            && isset($input['refund_to_aw_raf_enable']) && $input['refund_to_aw_raf_enable'] ? 1 : 0;
        $creditMemo->setAwRafIsReturnToAccount($enable);

        return $this;
    }
}
