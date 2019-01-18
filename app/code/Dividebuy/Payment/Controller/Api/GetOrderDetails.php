<?php
namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class GetOrderDetails extends \Magento\Framework\App\Action\Action
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
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutConfigHelper;

    /**
     * @var \Dividebuy\Payment\Model\Sales\Redirect
     */
    protected $_paymentRedirectModel;

    /**
     * Magento\Store\Model\Store
     */
    protected $_storeModel;
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
     * @param \Magento\Framework\Json\Helper\Data        $jsonHelper
     * @param \Dividebuy\CheckoutConfig\Helper\Data      $checkoutConfigHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dividebuy\Payment\Model\Sales\Redirect    $paymentRedirectModel
     * @param \Magento\Store\Model\Store                 $storeModel
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order $orderModel,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dividebuy\Payment\Model\Sales\Redirect $paymentRedirectModel,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_jsonHelper           = $jsonHelper;
        $this->_paymentHelper        = $paymentHelper;
        $this->_orderModel           = $orderModel;
        $this->_checkoutConfigHelper = $checkoutConfigHelper;
        $this->_paymentRedirectModel = $paymentRedirectModel;
        $this->_storeModel           = $storeModel;
        $this->_divideBuylogger         = $divideBuylogger;
        $this->_scopeConfig          = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Used to get Order details
     * 
     * @return mixed
     */
    public function execute()
    {
        $post     = trim(file_get_contents("php://input"));
        $postData = $this->_jsonHelper->jsonDecode($post);
        $storeToken           = $postData['storeToken'];
        $storeAuthentication  = $postData['storeAuthentication'];
        $storeCode            = $postData['retailerStoreCode'];
        $store                = $this->_storeModel->load($storeCode, 'code');
        $storeId              = $store->getStoreId();
        
        $authenticationStatus = $this->_paymentHelper->checkAuth($storeToken, $storeAuthentication, $storeId);
        
        if (!$postData) {
            $result = array("error" => 1, "success" => 0, "message" => "Please check eneterd data.");
            /* For error log code start*/
            $this->_paymentHelper->_prepareDataJSON($result);
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url : get order details \n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            } 
            /* Error log code end*/
            return;
        }
        if ($authenticationStatus) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'Store Authentication Failed',
            );
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
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        } else {
            $orderId = $postData['orderId'];
            if ($orderId) {
                $result = $this->_paymentRedirectModel->getRequest($orderId);
            }
        }
        $this->_paymentHelper->_prepareDataJSON($this->_jsonHelper->jsonDecode($result));
    }
}
