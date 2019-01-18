<?php

namespace Dividebuy\Payment\Controller\Order;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;

class GetOrderData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CheckoutSession                            $checkoutSession
     */
    public function __construct(Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory, CheckoutSession $checkoutSession) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Used to get zipcode and shipping method of current quote
     *
     * @return [type] [description]
     */
    public function execute()
    {
        $quote           = $this->_checkoutSession->getQuote();
        $zipcode         = $quote->getShippingAddress()->getPostcode();
        $quoteShipMethod = $quote->getShippingAddress()->getShippingMethod();

        echo json_encode(array(
            'zipcode'        => $zipcode,
            'shippingMethod' => $quoteShipMethod,
        ));
    }
}
