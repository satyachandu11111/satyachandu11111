<?php

namespace Dividebuy\Payment\Controller\Payment;

use Dividebuy\RetailerConfig\Helper\Data;
use Dividebuy\RetailerConfig\Helper\RetailerConfiguration;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;

class Response extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutConfigHelper;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Data                                       $retailerHelper
     * @param Session                                    $checkoutSession
     * @param \Magento\Sales\Model\Order                 $orderModel
     */
    public function __construct(Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $retailerHelper,
        Session $checkoutSession,
        RetailerConfiguration $retailerConfigurationHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Helper\Api $checkoutConfigHelper,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\Order $orderModel) {
        $this->_resultPageFactory           = $resultPageFactory;
        $this->_checkoutSession             = $checkoutSession;
        $this->_retailerHelper              = $retailerHelper;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        $this->_orderModel                  = $orderModel;
        $this->_jsonHelper                  = $jsonHelper;
        $this->_checkoutConfigHelper        = $checkoutConfigHelper;
        $this->_paymentHelper               = $paymentHelper;
        parent::__construct($context);
    }

    /**
     * Used to cancel or success the order
     *
     * @return mixed
     */
    public function execute()
    {
        $requestData = base64_decode($this->getRequest()->getParam('splashKey'));
        if (empty($requestData)) {
            $this->_redirect('checkout/cart/index', array('_secure' => true));
        }
        $requestArray = explode(':', $requestData);
        $this->_checkoutSession->setDividebuyOrderId($requestArray[0]);
        $order         = $this->_orderModel->load($requestArray[0]);
        $paymentMethod = $order->getPayment()->getMethod();
        if ($paymentMethod == \Dividebuy\Payment\Helper\Data::DIVIDEBUY_PAYMENT_CODE) {
            if ($requestArray[1] == "success") {
                // Sending order email only if order is DivideBuy and it is visible in order grid.
                if ($order->getHideDividebuy() == 1) {
                    /* Start - Code to get details of order through user order API */

                    // Getting the details which is to be send tp the API
                    $storeId             = $this->_retailerHelper->getStoreId();
                    $storeAuthentication = $this->_retailerHelper->getAuthNumber();
                    $storeToken          = $this->_retailerHelper->getTokenNumber();

                    $url     = $this->_retailerConfigurationHelper->getApiUrl($storeId) . 'api/getuserorder';
                    $request = array(
                        "storeOrderId"        => $order->getId(),
                        "storeAuthentication" => $storeAuthentication,
                        "storeToken"          => $storeToken,
                    );

                    $apiParams    = $this->_jsonHelper->jsonEncode($request);
                    $responseData = $this->_checkoutConfigHelper->sendRequest($url, $apiParams);

                    if (!empty($responseData["data"])) {
                        $orderDetails = $this->_jsonHelper->jsonDecode($responseData["data"], true);

                        // Calling complete order for setting latest order data
                        $this->_paymentHelper->completeOrder($order->getId(), $orderDetails);

                        $url          = $this->_retailerConfigurationHelper->getApiUrl($storeId) . 'api/syncretorder';
                        $responseData = $this->_checkoutConfigHelper->sendRequest($url, $apiParams);
                    }
                }
                /* End - Code to get details of order through user order API */
                
                $this->_redirect('dividebuy/payment/success');
            }
            if ($requestArray[1] == "cancel") {
                $this->cancel($requestArray[0]);
            }
        } else {
            $this->messageManager->addError(__("Non-DivideBuy Order"));
            $this->_redirect('checkout/cart');
            return;
        }
    }

    /**
     * Used to cancel the order
     *
     * @param  integer $orderId
     */
    public function cancel($orderId)
    {
        if (!empty($orderId)) {
            $order         = $this->_orderModel->load($orderId);
            $paymentMethod = $order->getPayment()->getMethod();
            //if ($order->getId() && $order->getState() != Mage_Sales_Model_Order::STATE_CANCELED && $order->getIsDividebuy()){
            if ($order->getId() && $paymentMethod == \Dividebuy\Payment\Helper\Data::DIVIDEBUY_PAYMENT_CODE) {
                $orderId = $order->getId();
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Order cancelled from dividebuy')->save();
                $params = array('order_id' => $orderId);
                $this->_forward('createcart', null, null, $params);
            } else {
                $this->_redirect('checkout/cart');
            }
        } else {
            $this->_redirect('checkout/cart');
        }
    }
}
