<?php

namespace Dividebuy\Payment\Controller\Payment;

use Dividebuy\RetailerConfig\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;

class Redirect extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Dividebuy\CheckoutConfig\Helper\Data
     */
    protected $_checkoutConfigHelper;

    /**
     * @var \Dividebuy\CheckoutConfig\Helper\Api
     */
    protected $_checkoutConfigAPIHelper;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dividebuy\CheckoutConfig\Helper\Data      $checkoutConfigHelper
     * @param Data                                       $retailerHelper
     * @param Session                                    $checkoutSession
     * @param \Dividebuy\RetailerConfig\Helper\RetailerConfiguration                                    $retailerConfigurationHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                                    $scopeConfig
     */
    public function __construct(Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Helper\Api $checkoutConfigAPIHelper,
        \Magento\Sales\Model\Order $orderModel,
        Data $retailerHelper,
        Session $checkoutSession,
        \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_resultPageFactory           = $resultPageFactory;
        $this->_checkoutSession             = $checkoutSession;
        $this->_retailerHelper              = $retailerHelper;
        $this->_orderModel                  = $orderModel;
        $this->_checkoutConfigHelper        = $checkoutConfigHelper;
        $this->_checkoutConfigAPIHelper        = $checkoutConfigAPIHelper;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        $this->_scopeConfig                 = $scopeConfig;
        $this->_jsonHelper                  = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Used to generate redirect URL with splash key
     *
     * @return string
     */
    public function execute()
    {
        if ($this->_checkoutSession->getTemparoryCart()) {
            $this->_checkoutConfigHelper->addSessionProducts();
        }
        $orderId    = $this->_checkoutSession->getLastOrderId();
        $order      = $this->_orderModel->load($orderId);
        $storeId    = $order->getStoreId();
        $retailerId = $this->_retailerHelper->getRetailerId();

        $tokenNumber       = $this->_scopeConfig->getValue('dividebuy/general/token_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $authenticationKey = $this->_scopeConfig->getValue('dividebuy/general/auth_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $splastKeyLeft  = base64_encode($tokenNumber . ':' . $orderId);
        $splastKeyRight = base64_encode($authenticationKey . ':' . $retailerId);
        $splash_key     = base64_encode($splastKeyLeft . ':' . $splastKeyRight);
        $redirectUrl    = $this->_retailerConfigurationHelper->getOrderUrl($this->_retailerHelper->getStoreId()) . "?splashKey=" . $splash_key;

        // Getting value of DivideBuy user email.
        $dividebuyUserEmail = $this->_checkoutSession->getDividebuyUserEmail();

        $phoneOrderToken = $this->_checkoutSession->getDividebuyPhoneOrderToken();

        // Sending order details to DivideBuy core API.
        if($dividebuyUserEmail){
            $request = array(
                'orderId'               => $orderId,
                'retailerId'            => $retailerId,
                'storeToken'            => $tokenNumber,
                'storeAuthentication'   => $authenticationKey,
                'email'                 => $dividebuyUserEmail,
                'name'                  => '',  
                'token'                 => $phoneOrderToken,
            );
            
            $request = $this->_jsonHelper->jsonEncode($request);;
            $url = $this->_retailerConfigurationHelper->getApiUrl($storeId).'api/pos/sendCheckoutUrlPos';
            $this->_checkoutConfigAPIHelper->sendRequest($url,$request);
        }

        if(!empty($phoneOrderToken)) {
            $redirectUrl .= "&token=".$phoneOrderToken;
        }

        $order->setState(\Magento\Sales\Model\Order::STATE_NEW)->setStatus(\Dividebuy\Payment\Helper\Data::DIVIDEBUY_ORDER_STATUS);
        $order->save();
        //$this->_redirectUrl($redirectUrl);
        return $this->resultRedirectFactory->create()->setUrl($redirectUrl);
    }
}
