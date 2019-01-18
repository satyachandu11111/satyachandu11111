<?php
namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class CreateGuarantorOrder extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Dividebuy\Payment\Helper\Order
     */
    protected $_paymentOrderHelper;

    /**
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutConfigHelper;

    /**
     * @var \Dividebuy\Payment\Model\Sales\Redirect
     */
    protected $_paymentRedirectModel;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
     /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;
    /**
     * @param Context                                    $context
     * @param \Magento\Sales\Model\Order                 $orderModel
     * @param \Dividebuy\Payment\Helper\Data             $paymentHelper
     * @param \Dividebuy\Payment\Helper\Order            $paymentOrderHelper
     * @param \Magento\Framework\Json\Helper\Data        $jsonHelper
     * @param \Dividebuy\CheckoutConfig\Helper\Data      $checkoutConfigHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dividebuy\Payment\Model\Sales\Redirect    $paymentRedirectModel
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order $orderModel,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Dividebuy\Payment\Helper\Order $paymentOrderHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dividebuy\Payment\Model\Sales\Redirect $paymentRedirectModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_jsonHelper           = $jsonHelper;
        $this->_paymentHelper        = $paymentHelper;
        $this->_paymentOrderHelper   = $paymentOrderHelper;
        $this->_orderModel           = $orderModel;
        $this->_checkoutConfigHelper = $checkoutConfigHelper;
        $this->_paymentRedirectModel = $paymentRedirectModel;
        $this->_divideBuylogger         = $divideBuylogger;
        $this->_scopeConfig          = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Used to create a guarantor order
     * 
     * @return void
     */
    public function execute()
    {
        $post     = trim(file_get_contents("php://input"));
        $postData = $this->_jsonHelper->jsonDecode($post);
        if (!$postData) {
            $result = array("error" => 1, "success" => 0, "message" => "There is a problem in retriving data", "status" => "406");
            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Gurantor api there is a problem in retriving the data\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            /* Error log code end*/
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }


        $orderId      = $postData['orderId'];
        $orderIsValid = $this->_loadValidOrder($orderId);

        if (!$orderIsValid) {
            $order = $this->_orderModel->load($postData['orderId']);
            $storeId = $order->getStoreId();
            $result = array("error" => 1, "success" => 0, "message" => "Order is no valid or not found", "status" => "404");
            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Gurantor api order is not valid or not found\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            /* Error log code end*/
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        // Check if order is valid or not.
        if ($orderIsValid == "true") {
            $order = $this->_orderModel->load($orderId);
            $this->cancelOrder($order);

            // Check current product stock for order products.
            $stockAvailable = $this->_paymentOrderHelper->getProductCurrentStock($order);

            if ($stockAvailable == "true") {
                $quoteId = $order->getQuoteId();

                // Creating new order getting data from previous order quote.
                $newOrderId = $this->_paymentOrderHelper->createNewOrder($quoteId);
                if ($newOrderId) {
                    $result = array("error" => 0, "success" => "ok", "orderId" => $newOrderId, "status" => "200");
                    /* For error log code start*/
                    $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
                    if($errorLogStatus == 1)
                    {
                        $dataResponse = "============\n";
                        $dataResponse .= "Url : Gurantor api order is created\n";
                        $dataResponse .= "Param :".$post."\n";
                        $dataResponse .= "Response :".json_encode($result)."\n";
                        $this->_divideBuylogger->info($dataResponse);    
                    }
                    /* Error log code end*/
                } else {
                    $result = array("error" => 1, "success" => 0, "message" => "Error while creating new order in retailer", "status" => "408");
                    /* For error log code start*/
                    $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
                    if($errorLogStatus == 1)
                    {
                        $dataResponse = "============\n";
                        $dataResponse .= "Url : Gurantor api Error while creating new order in retailer\n";
                        $dataResponse .= "Param :".$post."\n";
                        $dataResponse .= "Response :".json_encode($result)."\n";
                        $this->_divideBuylogger->info($dataResponse);    
                    }
                    /* Error log code end*/

                }
            } else {
                $result = array("error" => 1, "success" => 0, "message" => "Order stock is not available", "status" => "407");
                /* For error log code start*/
                $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
                if($errorLogStatus == 1)
                {
                    $dataResponse = "============\n";
                    $dataResponse .= "Url : Gurantor api Order stock is not available\n";
                    $dataResponse .= "Param :".$post."\n";
                    $dataResponse .= "Response :".json_encode($result)."\n";
                    $this->_divideBuylogger->info($dataResponse);    
                }
                /* Error log code end*/
            }
        } else {
            $result = array("error" => 1, "success" => 0, "message" => "Order not found", "status" => "403");
            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Gurantor api Order not found \n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }            
            /* Error log code end*/
        }
        $this->_paymentHelper->_prepareDataJSON($result);
    }

    /**
     * Try to load valid order by order_id and register it
     * 
     * @param int $orderId
     * @return bool
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

    /**
     * Used to cancel an order
     * 
     * @param type $order
     */
    protected function cancelOrder($order)
    {
        $order->cancel();

        // remove status history set in _setState
        $order->getStatusHistoryCollection(true);
        $order->save();
    }
}
