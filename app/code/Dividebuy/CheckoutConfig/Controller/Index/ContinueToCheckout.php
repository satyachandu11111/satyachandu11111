<?php

namespace Dividebuy\CheckoutConfig\Controller\Index;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutBlock;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

class ContinueToCheckout extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutBlock;

    /**
     * @var CacheTypeListInterface
     */
    protected $cache;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CheckoutSession                            $checkoutSession
     * @param CheckoutBlock                              $checkoutBlock
     * @param CacheTypeListInterface                     $cache
     * @param CustomerSession                            $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CheckoutSession $checkoutSession,
        CheckoutBlock $checkoutBlock,
        CacheTypeListInterface $cache,
        CustomerSession $customerSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutSession   = $checkoutSession;
        $this->_customerSession   = $customerSession;
        $this->_checkoutBlock     = $checkoutBlock;
        $this->cache              = $cache;
        parent::__construct($context);
    }

    /**
     * Redirect to the particular action according to the products in cart.
     */
    public function execute()
    {
        $this->cache->cleanType('full_page');
        $this->cache->cleanType('block_html');
        // Storing value of checkout session and customer session.
        $sessionGuest    = $this->_checkoutSession->getguest();
        $sessionCustomer = $this->_customerSession;

        $this->setCurrentShippingData();

        // Get count of dividebuy and non-dividebuy products in cart with use of Checkout Module
        $checkCart                = $this->_checkoutBlock->getItemArray();
        $nonDividebuyProductCount = $checkCart['nodividebuy'];
        $userLoggedIn             = 'false';
        // Check if current session is of guest
        if ($sessionGuest || $sessionCustomer->isLoggedIn() || $this->_checkoutSession->getCheckoutPage()) {
            // Check for non-dividebuy products count in cart and set flag value based on it.
            if ($nonDividebuyProductCount == 0) {
                $userLoggedIn = 'true_no_mixed_products';
            } else {
                $userLoggedIn = 'true_with_mixed_products';
            }
        }

        switch ($userLoggedIn) {

            // If there are only DivideBuy products in cart, display shipping information.
            case "true_no_mixed_products":
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('checkoutconfig/index/shippingmodal');
                return $resultRedirect;
                break;

            // If there are mixed products in cart, display seperated products in modal with grand total.
            case "true_with_mixed_products":
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('checkoutconfig/index/cartmodal');
                return $resultRedirect;
                break;

            // If user is neither logged in nor guest session, display login form.
            case "false":
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('checkoutconfig/index/userloginmodal');
                return $resultRedirect;
                break;
        }
    }

    /**
     * Sets the shipping method and amount in current checkout sesion
     */
    protected function setCurrentShippingData()
    {
        $this->_checkoutSession->unsShipping();
        $quote                = $this->_checkoutSession->getQuote();
        $quoteShipMethod      = $quote->getShippingAddress()->getShippingMethod();
        $quoteShipMethodPrice = $quote->getShippingAddress()->getShippingAmount();
        $shipping             = array($quoteShipMethod, $quoteShipMethodPrice);
        $this->_checkoutSession->setShipping($shipping);
    }
}
