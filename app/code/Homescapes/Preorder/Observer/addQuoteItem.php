<?php

namespace Homescapes\Preorder\Observer;

use Magento\Framework\Event\ObserverInterface;

class addQuoteItem implements ObserverInterface
{
    
protected $_checkoutSession;

protected $_productloader;  

protected $_helper;  

public function __construct (
    \Magento\Checkout\Model\Session $_checkoutSession,
    \Magento\Catalog\Model\ProductFactory $_productloader,
    \Homescapes\Preorder\Helper\Data $helper
    ) {
    $this->_productloader = $_productloader;
    $this->_checkoutSession = $_checkoutSession;
    $this->_helper = $helper;
}

 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getQuoteItem()->getProduct();
        $_product = $this->_productloader->create()->load($product->getId());
        $preOrderNote = $this->_helper->isPreOrder($_product);
        if($preOrderNote){
            $this->_checkoutSession->getQuote()->setPreorder(1);
        }
        
    }
    
}
