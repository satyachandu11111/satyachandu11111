<?php
namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;
use Dividebuy\RetailerConfig\Helper\RetailerConfiguration;

class AcceptCancelOrder extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_storeModel;

    /**
     * @param Context                                    $context
     * @param \Magento\Sales\Model\Order                 $orderModel
     * @param \Dividebuy\Payment\Helper\Data             $paymentHelper
     * @param \Magento\Framework\Json\Helper\Data        $jsonHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order $orderModel,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Store $storeModel,
        \Dividebuy\CheckoutConfig\Helper\Api $checkoutConfigHelper,
        RetailerConfiguration $retailerConfigurationHelper,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_resultPageFactory           = $resultPageFactory;
        $this->_jsonHelper                  = $jsonHelper;
        $this->_paymentHelper               = $paymentHelper;
        $this->_orderModel                  = $orderModel;
        $this->_scopeConfig                 = $scopeConfig;
        $this->_storeModel                  = $storeModel;
        $this->_divideBuylogger             = $divideBuylogger;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        $this->_checkoutConfigHelper        = $checkoutConfigHelper;
        parent::__construct($context);
    }

    /**
     * Used to checnge status of cancelled order to pending.
     *
     * @return mixed
     */
    public function execute()
    {
        $post     = trim(file_get_contents("php://input"));
        $postData = $this->_jsonHelper->jsonDecode($post);

        if (empty($postData)) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'There is a problem in retrieving data.',
                'status'  => '402',
            );
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        if (empty($postData["orderId"])) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'Please mention order id.',
                'status'  => '402',
            );
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        if (empty($postData["storeToken"]) || empty($postData["storeAuthentication"]) || empty($postData["retailerStoreCode"])) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'Authentication, token and store code are required.',
                'status'  => '402',
            );
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        $storeToken           = $postData['storeToken'];
        $storeAuthentication  = $postData['storeAuthentication'];
        $storeCode            = $postData['retailerStoreCode'];
        $store                = $this->_storeModel->load($storeCode, 'code');
        $storeId              = $store->getStoreId();
        $authenticationStatus = $this->_paymentHelper->checkAuth($storeToken, $storeAuthentication, $storeId);

        if(!empty($authenticationStatus)) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'Authentication Failed.',
                'status'  => '402',
            );
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        $orderId = $postData["orderId"];
        
        try
        {  
            $order = $this->_orderModel->load($orderId);

            if(empty($order->getId())) {
                $result = array(
                    'error'   => 1,
                    'success' => 0,
                    'message' => 'Order not found.',
                    'status'  => '402',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }

            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus('pending');
            $order->setBaseDiscountCanceled(0);
            $order->setBaseShippingCanceled(0);
            $order->setBaseSubtotalCanceled(0);
            $order->setBaseTaxCanceled(0);
            $order->setBaseTotalCanceled(0);
            $order->setDiscountCanceled(0);
            $order->setShippingCanceled(0);
            $order->setSubtotalCanceled(0);
            $order->setTaxCanceled(0);
            $order->setTotalCanceled(0);
            foreach($order->getAllItems() as $item){
                $item->setQtyCanceled(0);
                $item->setTaxCanceled(0);
                $item->setHiddenTaxCanceled(0);
                $item->save();
            }
            $order->save();
        } catch (\Exception $e) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'There is a problem in accepting this cancel order.',
                'exceptionMessage' => $e->getMessage(),
                'status'  => '402',
            );
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        if($postData["successOrder"] == 1) {
            $completeOrder = $this->_orderModel->load($orderId);

            if ($completeOrder->getHideDividebuy() == 1) {

                /* Start - Code to get details of order through user order API */

                // Getting the details which is to be send tp the API
                $storeId             = $storeId;
                $storeAuthentication = $postData['storeAuthentication'];
                $storeToken          = $postData['storeToken'];

                $url     = $this->_retailerConfigurationHelper->getApiUrl($storeId) . 'api/getuserorder';
                $request = array(
                    "storeOrderId"        => $completeOrder->getId(),
                    "storeAuthentication" => $storeAuthentication,
                    "storeToken"          => $storeToken,
                );

                $apiParams    = $this->_jsonHelper->jsonEncode($request);
                $responseData = $this->_checkoutConfigHelper->sendRequest($url, $apiParams);

                if (!empty($responseData["data"])) {
                    $orderDetails = $this->_jsonHelper->jsonDecode($responseData["data"], true);

                    // Calling complete order for setting latest order data
                    $this->_paymentHelper->completeOrder($orderId, $orderDetails);

                    $url          = $this->_retailerConfigurationHelper->getApiUrl($storeId) . 'api/syncretorder';
                    $responseData = $this->_checkoutConfigHelper->sendRequest($url, $apiParams);
                    $completeOrder->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $completeOrder->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $completeOrder->save();
                } else {
                    $result = array(
                        'error'   => 0,
                        'success' => 1,
                        'message' => 'Order not found in checkout',
                        'status'  => 'ok',
                    );

                    $this->_paymentHelper->_prepareDataJSON($result);
                    return;      
                }
            }
        }

        $result = array(
            'error'   => 0,
            'success' => 1,
            'message' => 'Cancelled order is successfully accepted.',
            'status'  => 'ok',
        );
        $this->_paymentHelper->_prepareDataJSON($result);
        return;
    }
}
