<?php
namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class CancelOrder extends \Magento\Framework\App\Action\Action
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
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_jsonHelper        = $jsonHelper;
        $this->_paymentHelper     = $paymentHelper;
        $this->_orderModel        = $orderModel;
        $this->_scopeConfig          = $scopeConfig;
        $this->_divideBuylogger         = $divideBuylogger;
        parent::__construct($context);
    }

    /**
     * Used to cancel an order
     * 
     * @return mixed
     */
    public function execute()
    {
        $post     = trim(file_get_contents("php://input"));
        $postData = $this->_jsonHelper->jsonDecode($post);
        $order = $this->_orderModel
            ->load($postData['store_order_id']);
        $storeId = $order->getStoreId();
        if ($order->getId()) {
            $result = $this->_cancelOrder($order);

            // Checking if the delete user order flag has 1 than deleting order completely
            if(isset($postData["delete_user_order"]) && $postData["delete_user_order"] == 1) {
                $this->_paymentHelper->deleteOrder($order, $postData ,$storeId);
            }
            

            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url :Cancel order api \n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            /* Error log code End*/  
        } else {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'message' => 'order not found',
                'status'  => '403',
            );
            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url :Cancel order api order not found\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            /* Error log code End*/  
        }
        $this->_paymentHelper->_prepareDataJSON($result);
    }

    /**
     * To cancel an order
     * 
     * @param $order object
     * @return status of the order with cancelled
     */
    protected function _cancelOrder($order = null)
    {
        $paymentMethod = $order->getPayment()->getMethod();
        $result        = array();
        if ($order->getState() == \Magento\Sales\Model\Order::STATE_CANCELED) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'status'  => '405',
                'message' => 'Order is already cancelled',
            );
        } elseif ($paymentMethod != \Dividebuy\Payment\Helper\Data::DIVIDEBUY_PAYMENT_CODE) {
            $result = array(
                'error'   => 1,
                'success' => 0,
                'status'  => '402',
                'message' => 'Non-DivideBuy order',
            );
        } else {
            $order->cancel()
            ->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Order cancelled from dividebuy')
            ->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Order cancelled from dividebuy')
            ->save();
            $result = array(
                'error'    => 0,
                'success'  => 1,
                'status'   => 'ok',
                'order_id' => $order->getId(),
            );
        }
        return $result;
    }
}
