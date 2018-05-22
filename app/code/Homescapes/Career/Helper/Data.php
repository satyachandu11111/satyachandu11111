<?php

namespace Homescapes\Career\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig;

	public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }
 
 
    public function getTitle(){
     
       return $this->_scopeConfig->getValue('career/email/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
     
    }

    public function getJobtype(){
     
       return $this->_scopeConfig->getValue('career/email/job_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
     
    }

    public function getTemplateId(){
     
       return $this->_scopeConfig->getValue('career/email/show_email_template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
     
    }

    public function getOwnerEmail(){
     
       return $this->_scopeConfig->getValue('career/email/emails', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
     
    }

}

