<?php

namespace Dividebuy\Payment\Controller\Api;

use Dividebuy\RetailerConfig\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;

class VerifyPostCode extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_shippingConfig;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_addressdata;

    /**
     * @var \Magento\Quote\Model
     */
    protected $_quoteModel;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

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
        Session $checkoutSession,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\Registry $registry,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote $quoteModel
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutSession   = $checkoutSession;
        $this->_retailerHelper    = $retailerHelper;
        $this->_orderModel        = $orderModel;
        $this->_registry          = $registry;
        $this->_cart              = $cart;
        $this->_jsonHelper        = $jsonHelper;
        $this->_shippingConfig    = $shippingConfig;
        $this->_paymentHelper     = $paymentHelper;
        $this->_quoteModel        = $quoteModel;
        $this->_scopeConfig       = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Used to check zipcode is valid and shipping rates are changed or not
     */
    public function execute()
    {
        $post     = trim(file_get_contents("php://input"));
        $postData = $this->_jsonHelper->jsonDecode($post);
        if (!$postData) {
            $result = array("error" => 1, "success" => 0, "message" => "There is a problem in retriving data", "status" => "406");
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }
        $orderId      = $postData['orderId'];
        $orderIsValid = $this->_loadValidOrder($orderId);

        if ($orderIsValid == "true") {
            $shippingChanged     = "false";
            $zipcode             = $postData['userPostcode'];
            $country             = 'GB';

            // Loading quote from order
            $order               = $this->_orderModel->load($orderId);
            $quoteId             = $this->_orderModel->load($orderId)->getQuoteId();
            $quote               = $this->_quoteModel->load($quoteId);

            $orderShippingMethod = $order->getShippingMethod();
            $orderShippingAmount = $order->getShippingAmount();

            // Setting shipping address zipcode and country
            $address             = $quote->getShippingAddress();
            $address->setCountryId($country)->setPostcode($zipcode);
            $quote->save();

            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->getShippingAddress()->collectShippingRates();

            $_rates        = $quote->getShippingAddress()->getShippingRatesCollection();
            $shippingRates = array();
            foreach ($_rates as $rate) {
                if (!$rate->getRateId()) {
                    if ($rate->getCode() == $orderShippingMethod) {

                        // comparing price of shipping methods
                        if (number_format($rate->getPrice(), 2) != number_format($orderShippingAmount, 2)) {
                            $shippingChanged = "true";
                        }
                        break;
                    }
                }
            }
            // Checking if shipping data has been changed or not.
            if ($shippingChanged == "true") {
                $result = array(
                    'error'   => 1,
                    'success' => 0,
                    'message' => "There is change in shipping methods.",
                    'status'  => '408',
                );
            } else {
                $result = array(
                    'error'   => 0,
                    'success' => 1,
                    'message' => "ok",
                    'status'  => '200',
                );
            }

        } else {
            $result = array("error" => 1, "success" => 0, "message" => "Order not found", "status" => "403");
        }
        $this->_paymentHelper->_prepareDataJSON($result);
        return;
    }

    /**
     * User to load order and check order is valid or not
     * 
     * @param $orderId
     * @return boolean
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }

        $orderIsValid = false;
        if (!$orderId) {
            $orderIsValid = "false";
        }

        $order = $this->_orderModel->load($orderId);
        if ($order->getId()) {
            $orderIsValid = "true";
        }
        
        return $orderIsValid;
    }
}
