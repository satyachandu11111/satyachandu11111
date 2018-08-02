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

class ResetCollectData implements ObserverInterface
{

    protected $_collecthelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;


    /**
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Jjcommerce\CollectPlus\Helper\Data $_collecthelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_collecthelper = $_collecthelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
    }

    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_checkoutSession->getQuote();

        if ($quote->getId()) {
            $quote->setSmsAlert('');
            $quote->setAgentData('');
            $quote->save();
        }
    }
}
