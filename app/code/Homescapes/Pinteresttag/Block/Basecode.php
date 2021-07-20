<?php
namespace Homescapes\Pinteresttag\Block;

class Basecode extends \Magento\Framework\View\Element\Template
{
    
    protected $_customerSession;
         
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    ) {
        $this->_customerSession = $customerSession->create();
        parent::__construct($context, $data);
    }
     
    public function getLoggedinCustomerId() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getId();
        }
        return false;
    }
 
    public function getCustomerData() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomerData();
        }
        return false;
    }
    
    
}
