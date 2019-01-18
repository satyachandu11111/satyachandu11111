<?php

namespace Dividebuy\Payment\Controller\Payment;

use Magento\Framework\App\Action\Context;

class Success extends \Magento\Framework\App\Action\Action
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
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Dividebuy\Payment\Helper\Data             $paymentHelper
     * @param array                                      $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\Store $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper,
        array $data = []
    ) {
        $this->_storeManager                = $storeManager;
        $this->_resultPageFactory           = $resultPageFactory;
        $this->_checkoutSession             = $checkoutSession;
        $this->_paymentHelper               = $paymentHelper;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        parent::__construct($context);
    }

    /**
     * Used to redirect user to the success page
     */
    public function execute()
    {

        //Checking for phone order session and redirecting to portal.
        $phoneOrderTokenSession = $this->_checkoutSession->getDividebuyPhoneOrderToken();
        if($phoneOrderTokenSession) {
            // Getting store ID.
            $store     = $this->_storeManager->load("default", "code");
            $storeId   = $store->getId();
            $portalUrl = $this->_retailerConfigurationHelper->getPortalUrl($storeId);
            $this->_redirect($portalUrl);
            $this->_checkoutSession->unsDividebuyPhoneOrderToken();
        }
        
        if ($this->_checkoutSession->getDividebuyOrderId()) {
            $this->_paymentHelper->generateInvoice($this->_checkoutSession->getDividebuyOrderId());
            $resultPage = $this->_resultPageFactory->create();
            // $this->_checkoutSession->unsDividebuyOrderId();
            return $resultPage;
        } else {
            $this->_redirect("checkout/cart");
        }
    }
}
