<?php

namespace Homescapes\Orderswatch\Controller\Index;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    protected $resultPageFactory;    


    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->resultPageFactory = $pageFactory;
        
        parent::__construct($context);        
    }

    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();        
        
        return $resultPage;
    }
}
