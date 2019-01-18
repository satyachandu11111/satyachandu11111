<?php
namespace Dividebuy\Payment\Block;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;

class OrderSuccess extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Sales\Model\Order\Config                $orderConfig
     * @param \Magento\Framework\App\Http\Context              $httpContext
     * @param \Magento\Sales\Model\Order                       $orderModel
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\Order $orderModel,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig     = $orderConfig;
        $this->_isScopePrivate  = true;
        $this->httpContext      = $httpContext;
        $this->_orderModel      = $orderModel;
    }

    /**
     * Render additional order information lines and return result html
     *
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepares block of data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $orderId = $this->_checkoutSession->getDividebuyOrderId();
        $order   = $this->_orderModel->load($orderId);
        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url'   => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url'        => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order'  => $this->isVisible($order),
                'can_view_order'   => $this->canViewOrder($order),
                'order_id'         => $order->getIncrementId(),
            ]
        );
    }

    /**
     * Is order visible or not
     *
     * @param Order $order
     * @return bool
     */
    protected function isVisible(Order $order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

    /**
     * Can view order
     *
     * @param Order $order
     * @return bool
     */
    protected function canViewOrder(Order $order)
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH)
        && $this->isVisible($order);
    }

    /**
     * get dividebuy order Id from checkout session
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->_checkoutSession->getDividebuyOrderId();
    }

    /**
     * get dividebuy order increment Id from checkout session
     *
     * @return integer
     */
    public function getOrderIncrementId()
    {
        $orderId = $this->_checkoutSession->getDividebuyOrderId();
        $order = $this->_orderModel->load($orderId);
        return $order->getIncrementId();
    }

    /**
     * Get CheckoutSession Object
     *
     * @return object
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
