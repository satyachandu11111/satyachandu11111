<?php

namespace Homescapes\Preorder\Observer;

use Magento\Framework\Event\ObserverInterface;

class preorderDataSave implements ObserverInterface
{
    protected $quoteFactory;


    public function __construct(        
        \Magento\Quote\Model\QuoteFactory $quoteFactory
     ) {        
        $this->quoteFactory = $quoteFactory;
    }


 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        //$product = $observer->getQuoteItem()->getProduct();
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);        
        
        if($quote->getPreorder())     
            $order->setPreorder(1);
        
        
        return $this;    
    }
    
}

