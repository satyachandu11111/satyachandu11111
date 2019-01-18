<?php
namespace Dividebuy\Payment\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class RefundObserver implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteloader;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Dividebuy\CheckoutConfig\Helper\Api
     */
    protected $_apiHelper;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\Quote\Model\QuoteFactory                      $quoteFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface     $config
     * @param \Magento\Store\Model\StoreManagerInterface             $storeManager
     * @param \Magento\Sales\Model\Order                             $orderModel
     * @param \Dividebuy\CheckoutConfig\Helper\Api                   $apiHelper
     * @param \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper
     * @param \Magento\Framework\Json\Helper\Data                    $jsonHelper
     * @param \Magento\Framework\App\RequestInterface                $request
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order $orderModel,
        \Dividebuy\CheckoutConfig\Helper\Api $apiHelper,
        \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_quoteloader                 = $quoteFactory;
        $this->_config                      = $config;
        $this->storeManager                 = $storeManager;
        $this->_orderModel                  = $orderModel;
        $this->_apiHelper                   = $apiHelper;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        $this->_jsonHelper                  = $jsonHelper;
        $this->_request                     = $request;
    }

    /**
     * Used to set hide dividebuy field to 1
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {

        $postData      = $this->_request->getParams();
        $request       = array();
        $orderProducts = $observer->getCreditmemo()->getItems();
        $i             = 0;

        $orderId       = $observer->getCreditmemo()->getOrderId();
        $order         = $this->_orderModel->load($orderId);
        $paymentMethod = $order->getPayment()->getMethod();

        // Condition to check whether the order is dividebuy or not
        if ($paymentMethod == \Dividebuy\Payment\Helper\Data::DIVIDEBUY_PAYMENT_CODE) {
            $creditMemoDetails = $observer->getCreditmemo()->getData();

            // Refund Details
            foreach ($orderProducts as $product) {
                if ($product->getData("base_price") > 0) {
                    $request["product"][$i]["sku"]          = $product->getData("sku");
                    $request["product"][$i]["productName"]  = $product->getData("name");
                    $request["product"][$i]["qty"]          = $product->getData("qty");
                    $request["product"][$i]["rowTotal"]     = $product->getData("row_total");
                    $request["product"][$i]["rowInclTotal"] = $product->getData("row_total_incl_tax");
                    $request["product"][$i]["rowInclTotal"] = $product->getData("row_total_incl_tax");
                }
            }
            if(isset($creditMemoDetails["discount_amount"])) {
                $request["discountAmount"]  = str_replace("-", "", $creditMemoDetails["discount_amount"]);
            }

            $request["totalRefund"]        = $observer->getCreditmemo()->getBaseGrandTotal();
            $request["orderId"]            = $observer->getCreditmemo()->getOrderId();
            $request["reason"]             = $postData["creditmemo"]["comment_text"];
            $request["adjustmentRefund"]   = $postData["creditmemo"]["adjustment_positive"];
            $request["adjustmentFee"]      = $postData["creditmemo"]["adjustment_negative"];
            $request["taxAmount"]          = $creditMemoDetails["tax_amount"];
            $request["reason"]             = $postData["creditmemo"]["comment_text"];
            $request["shippingCostAmount"] = $creditMemoDetails["shipping_amount"];
            $request["shippingTaxAmount"]  = $creditMemoDetails["shipping_incl_tax"];

            $request["refundType"] = "FULL";
            if ($order->getData("base_grand_total") != $request["totalRefund"]) {
                $request["refundType"] = "PARTIAL";
            }

            // Retailer Details
            $storeId = $order->getStoreId();
            $url     = $this->_retailerConfigurationHelper
                ->getApiUrl($storeId) . 'api/refund';

            $request["retailer"]["retailerId"] = $this->_config->getValue("dividebuy/general/retailer_id",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $request["retailer"]["storeAuthentication"] = $this->_config->getValue("dividebuy/general/auth_number",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $request["retailer"]["storeToken"] = $this->_config->getValue("dividebuy/general/token_number",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            
            $params   = $this->_jsonHelper->jsonEncode($request);
            
            $response = $this->_apiHelper->sendRequest($url, $params);
        }
    }
}
