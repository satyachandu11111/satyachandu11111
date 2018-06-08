<?php

namespace Homescapes\Preorder\Observer;

use Magento\Framework\Event\ObserverInterface;

class setProductName implements ObserverInterface
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
        $item = $observer->getQuoteItem();
        $_product = $this->_productloader->create()->load($item->getProductId());
        $preOrderNote = $this->_helper->isPreOrder($_product);
        if($preOrderNote){
            $item->setName($item->getName()." (preorder)");
        }
        
    }
    
}

