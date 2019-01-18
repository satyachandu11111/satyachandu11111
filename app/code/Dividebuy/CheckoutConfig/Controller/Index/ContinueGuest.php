<?php

namespace Dividebuy\CheckoutConfig\Controller\Index;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;

class ContinueGuest extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CheckoutSession                            $checkoutSession
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CheckoutSession $checkoutSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutSession   = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Check the user credentials and generates session based on it
     */
    public function execute()
    {
        // Creating checkout session for guest user.
        $this->_checkoutSession->setguest("1");

        // Proceed to checkout action.
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('checkoutconfig/index/continuetocheckout');
        return $resultRedirect;
    }
}
