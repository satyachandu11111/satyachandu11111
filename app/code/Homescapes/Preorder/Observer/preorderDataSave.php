<?php

namespace Homescapes\Preorder\Observer;

use Magento\Framework\Event\ObserverInterface;

class preorderDataSave implements ObserverInterface
{
    


 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        //$product = $observer->getQuoteItem()->getProduct();
        $quote = $observer->getQuote();
        $order = $observer->getOrder();   
        if($quote->getPreorder())     
            $order->setPreorder(1);
    }
    
}

