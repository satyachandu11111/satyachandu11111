<?php
namespace Dividebuy\Payment\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteloader;

    /**
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->_quoteloader = $quoteFactory;
    }

    /**
     * Used to set hide dividebuy field to 1
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $order    = $observer->getEvent()->getOrder();
        $quoteId  = $order->getQuoteId();
        $quoteObj = $this->_quoteloader->create()->load($quoteId);

        $paymentMethod = $quoteObj->getPayment()->getMethod();

        if ($paymentMethod == \Dividebuy\Payment\Model\Dbpayment::PAYMENT_METHOD_DIVIDEBUY_CODE) {
            $order->setHideDividebuy(1);
            $order->save();
        }
    }
}
