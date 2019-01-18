<?php
namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class SuccessOrder extends \Magento\Framework\App\Action\Action
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
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;
    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;
    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $_orderSender;

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
        $this->_paymentHelper     = $paymentHelper;
        $this->_jsonHelper        = $jsonHelper;
        $this->_orderModel        = $orderModel;
        $this->_scopeConfig       = $scopeConfig;
        $this->_divideBuylogger   = $divideBuylogger;
        parent::__construct($context);
    }

    /**
     * Process the success order
     *
     * @return [type] [description]
     */
    public function execute()
    {
        $post             = trim(file_get_contents("php://input"));
        $postData         = $this->_jsonHelper->jsonDecode($post);
        $orderId          = $postData['store_order_id'];
        $address          = $postData['address'];
        $customerEmail    = $postData['customer_email'];
        $orderTime        = $postData['orderTime'];
        $orderReferenceId = $postData['laravel_order_ref_id'];
        $isPhoneOrderEnabled = $postData['is_phone_order_enabled'];

        $street1 = $address['house_number'] . ' ' . $address['house_name'] . ', ' . $address['street'];
        $street1 = ltrim($street1, ", ");
        $street2 = $address['address2'];
        $street  = array(
            '0' => $street1,
            '1' => $street2,
        );

        $userAddress = array(
            'prefix'    => $address['prefix'],
            'firstname' => $address['first_name'],
            'lastname'  => $address['last_name'],
            'street'    => $street,
            'postcode'  => $address['postcode'],
            'region'    => $address['region'],
            'city'      => $address['city'],
            'email'     => $customerEmail,
            'telephone' => $address['contact_number'],
        );

        $order  = $this->_orderModel->load($orderId);
        $result = $this->_paymentHelper->setOrderData($order, $userAddress);

        if ($order->getId()) {
            // Sending order email only if order is DivideBuy and it is visible in order grid.
            if ($order->getHideDividebuy() == 1) {
                $orderEmail    = $this->_orderModel->loadByIncrementId($order->getIncrementId());
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objectManager->create('\Magento\Sales\Model\OrderNotifier')->notify($orderEmail);
                $orderEmail->setHideDividebuy(0);

                // Checking if order is placed via phone order.
                if($isPhoneOrderEnabled == 1){
                    $orderEmail->addStatusHistoryComment('DivideBuy order authenticated via phone order. Transaction ID : "'. $orderReferenceId.'"', \Magento\Sales\Model\Order::STATE_PROCESSING);
                }else{
                    $orderEmail->addStatusHistoryComment('DivideBuy order authenticated. Transaction ID : "'. $orderReferenceId.'"', \Magento\Sales\Model\Order::STATE_PROCESSING);
                }
                $orderEmail->save();
            }

        } else {
            $result = array(
                'error'           => 1,
                'success'         => 0,
                'message'         => 'order not found',
                'response_status' => '404',
            );
        }
        $this->_paymentHelper->_prepareDataJSON($result);
    }
}
