<?php

namespace Dividebuy\Payment\Controller\Payment;

use Dividebuy\RetailerConfig\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;

class CreateCart extends \Magento\Framework\App\Action\Action
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
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Data                                       $retailerHelper
     * @param Session                                    $checkoutSession
     * @param \Magento\Sales\Model\Order                 $orderModel
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Checkout\Model\Cart               $cart
     */
    public function __construct(Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $retailerHelper,
        Session $checkoutSession,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Cart $cart) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutSession   = $checkoutSession;
        $this->_retailerHelper    = $retailerHelper;
        $this->_orderModel        = $orderModel;
        $this->_registry          = $registry;
        $this->_cart              = $cart;
        parent::__construct($context);
    }

    /**
     * Used to regenerate cart after order is cancelled from dividebuy
     */
    public function execute()
    {
        $orderId       = $this->getRequest()->getParam("order_id");
        $order         = $this->_orderModel->load($orderId);
        $cart          = $this->_cart;
        $cartTruncated = false;

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                } else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                $this->_redirect('checkout/cart');
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
                );
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->messageManager->addError(__('Your DivideBuy order has been cancelled for you to amend your basket.'));
        $this->_redirect('checkout/cart');
    }
}
