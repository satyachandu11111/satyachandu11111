<?php
namespace Dividebuy\CheckoutConfig\Controller\Index;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutBlock;
use Magento\Framework\App\Action\Context;

class CartModal extends \Magento\Framework\App\Action\Action
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
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CheckoutBlock                              $checkoutBlock
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CheckoutBlock $checkoutBlock
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutBlock     = $checkoutBlock;
        parent::__construct($context);
    }

    /**
     * Used to load the cart_items.phtml file
     */
    public function execute()
    {
        $modalBlock = $this->_checkoutBlock
            ->getLayout()
            ->createBlock('Dividebuy\CheckoutConfig\Block\Cart')
            ->setTemplate('Dividebuy_CheckoutConfig::dividebuy/cart/modal/cart_items.phtml')
            ->toHtml();

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($modalBlock);
        return;
    }
}
