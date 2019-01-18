<?php

namespace Dividebuy\CheckoutConfig\Controller\Index;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutBlock;
use Dividebuy\CheckoutConfig\Helper\Data as CheckoutHelper;
use Dividebuy\RetailerConfig\Helper\Data as RetailerHelper;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Api\CartRepositoryInterface;

class GetShippingMethods extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_cartModel;

    /**
     * @var \Dividebuy\CheckoutConfig\Model\CartModel
     */
    protected $_divideBuyCartModel;

    /**
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutBlock;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $_cartRepositoryInterface;

    /**
     * @var RetailerHelper
     */
    protected $_retailerHelper;

    /**
     * @var CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * @param CheckoutHelper                             $checkoutHelper
     * @param RetailerHelper                             $retailerHelper
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param Cart                                       $cartModel
     * @param CheckoutBlock                              $checkoutBlock
     * @param CartRepositoryInterface                    $cartRepositoryInterface
     * @param \Dividebuy\CheckoutConfig\Model\CartModel  $dividebuyCartModel
     */
    public function __construct(
        CheckoutHelper $checkoutHelper,
        RetailerHelper $retailerHelper,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        Cart $cartModel,
        CheckoutBlock $checkoutBlock,
        CartRepositoryInterface $cartRepositoryInterface,
        \Dividebuy\CheckoutConfig\Model\CartModel $dividebuyCartModel
    ) {
        $this->_resultPageFactory       = $resultPageFactory;
        $this->_cartModel               = $cartModel;
        $this->_checkoutBlock           = $checkoutBlock;
        $this->_cartRepositoryInterface = $cartRepositoryInterface;
        $this->_checkoutHelper          = $checkoutHelper;
        $this->_retailerHelper          = $retailerHelper;
        $this->_checkoutSession         = $checkoutSession;
        $this->_divideBuyCartModel      = $dividebuyCartModel;
        parent::__construct($context);
    }

    /**
     * Used to load the shipping_methods.phtml file.
     */
    public function execute()
    {
        $storeId = $this->_retailerHelper->getStoreId();
        $zipcode = $this->getRequest()->getParam('user_postcode');

        // Checking if DivideBuy ships has allowed to ship items for entered postcode by user.
        $zipcodeVerification = $this->_checkoutHelper->getDividebuyPostcodes($zipcode);
        if (!$zipcodeVerification) {
            echo "<font color='red'>" . $this->_checkoutHelper->showPostcodeMsg() . "</font>";
            return;
        }

        $this->_updateShippingData();
        $modalBlock = $this->_checkoutBlock
            ->getLayout()
            ->createBlock('Dividebuy\CheckoutConfig\Block\Cart')
            ->setTemplate('Dividebuy_CheckoutConfig::dividebuy/cart/modal/shipping_methods.phtml')
            ->toHtml();

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($modalBlock);
        return;
    }

    /**
     * Sets zipcode and country code for current shipping addresses.
     */
    protected function _updateShippingData()
    {
        $zipcode = $this->getRequest()->getParam('user_postcode');
        $country = 'GB';

        // Update the cart's quote.
        $address = $this->_cartModel->getQuote()->getShippingAddress();
        $address->setCountryId($country)
            ->setPostcode($zipcode)
            ->setCollectShippingrates(true);
        $this->_cartModel->save();

    }
}
