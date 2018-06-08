<?php

namespace Homescapes\Preorder\Observer;

use Magento\Framework\Event\ObserverInterface;

class removeQuoteItem implements ObserverInterface
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
        $flag = 0;
        $product = $observer->getQuoteItem()->getProduct();
        
        $quoteAllVisibleItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        $productIds = array();
        foreach($quoteAllVisibleItems as $item){
            $productIds[] = $item->getProductId();
            $_product = $this->_productloader->create()->load($item->getProductId());
            $preOrderNote = $this->_helper->isPreOrder($_product);
            if($preOrderNote){
                $flag = 1;
                break;
                        
            }
        }

        
        $this->_checkoutSession->getQuote()->setPreorder($flag);
        
        
    }
    
}

