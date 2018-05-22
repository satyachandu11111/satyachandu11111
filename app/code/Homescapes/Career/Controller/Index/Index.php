<?php

namespace Homescapes\Career\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends Action
{
    protected $resultPageFactory;
    protected $_helper;


    public function __construct(Context $context,  \Homescapes\Career\Helper\Data $helper, PageFactory $pageFactory)
    {
        $this->resultPageFactory = $pageFactory;
        
        parent::__construct($context);
        $this->_helper = $helper;
    }

    public function execute()
    {

        $title = $this->_helper->getTitle();

        if(!$title){
          $title = 'Career';
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($title);
        
        return $resultPage;
    }
}