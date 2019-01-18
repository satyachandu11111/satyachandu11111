<?php

namespace Dividebuy\CheckoutConfig\Controller\Index;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutBlock;
use Magento\Framework\App\Action\Context;

class ShippingModal extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutBlock;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Dividebuy\CheckoutConfig\Model\CartModel
     */
    protected $_cartModel;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param CheckoutBlock                              $checkoutBlock
     * @param \Dividebuy\CheckoutConfig\Model\CartModel  $cartModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        CheckoutBlock $checkoutBlock,
        \Dividebuy\CheckoutConfig\Model\CartModel $cartModel
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutBlock     = $checkoutBlock;
        $this->_cartModel         = $cartModel;
        $this->_checkoutSession   = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * Used to load the shipping_information.phtml file.
     */
    public function execute()
    {
        $this->_checkoutBlock->removeNonDivideBuyProducts();

        $modalBlock = $this->_checkoutBlock
            ->getLayout()
            ->createBlock('Dividebuy\CheckoutConfig\Block\Cart')
            ->setTemplate('Dividebuy_CheckoutConfig::dividebuy/cart/modal/shipping_information.phtml')
            ->toHtml();

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($modalBlock);
        return;
    }
}
