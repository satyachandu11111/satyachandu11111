<?php

namespace Dividebuy\Payment\Controller\Api;

use Dividebuy\RetailerConfig\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;

class VerifyToken extends \Magento\Framework\App\Action\Action
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
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_retailerHelper;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param Context                                            $context
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param Data                                               $retailerHelper
     * @param Session                                            $checkoutSession
     * @param \Magento\Sales\Model\Order                         $orderModel
     * @param \Magento\Framework\Registry                        $registry
     * @param \Dividebuy\Payment\Helper\Data                     $paymentHelper
     * @param \Magento\Checkout\Model\Cart                       $cart
     * @param \Magento\Shipping\Model\Config                     $shippingConfig
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote                         $quoteModel
     */
    public function __construct(Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $retailerHelper,
        CheckoutSession $checkoutSession,
        \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Dividebuy\CheckoutConfig\Helper\Api $checkoutConfigApiHelper
    ) {
        $this->_resultPageFactory           = $resultPageFactory;
        $this->_retailerHelper              = $retailerHelper;
        $this->_checkoutSession             = $checkoutSession;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        $this->_jsonHelper                  = $jsonHelper;
        $this->_scopeConfig                 = $scopeConfig;
        $this->_checkoutConfigApiHelper     = $checkoutConfigApiHelper;
        parent::__construct($context);
    }

    /**
     * Used to check zipcode is valid and shipping rates are changed or not
     */
    public function execute()
    {
        $phoneOrderToken = $this->getRequest()->getParam('token');

        if (!empty($phoneOrderToken)) {
            $storeId = $this->_retailerHelper->getStoreId();
            $url     = $this->_retailerConfigurationHelper->getApiUrl($storeId).'api/verify-token-permission';

            $request = array(
                "token" => $phoneOrderToken,
            );
            $request = $this->_jsonHelper->jsonEncode($request);
            $response = $this->_checkoutConfigApiHelper->sendRequest($url, $request);


            if(!isset($response["error"]) && empty($response["error"])) {
                //Checking for phone order session
                $phoneOrderTokenSession = $this->_checkoutSession->getDividebuyPhoneOrderToken();
                if($phoneOrderTokenSession) {
                    $this->_checkoutSession->unsDividebuyPhoneOrderToken();
                }

                //Setting up a new session
                $this->_checkoutSession->setDividebuyPhoneOrderToken($phoneOrderToken);

                // Checking if checkout is available or not.
                if($response['is_checkout_allowed'] == false){
                    // Checking for existing session for DivideBuy checkout.
                    $dividebuyCheckoutSession = $this->_checkoutSession->getDividebuyCheckoutSession();
                    if($dividebuyCheckoutSession){
                        $this->_checkoutSession->unsDividebuyCheckoutSession();
                    }

                    // Setting up new checkout session.
                    $this->_checkoutSession->setDividebuyCheckoutSession("no");
                }
                
                $this->_redirect('/?token='.$phoneOrderToken);
            }
        }

        //Redirecting to home page 
        $this->_redirect('/');
    }
}
