<?php
namespace Homescapes\General\Block;

class Websites extends \Magento\Framework\View\Element\Template 
{

    protected $_storeManager;  
    
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->_storeManager = $storeManager;
		parent::__construct(
            $context
        );
	}


	public function getWebsites() 
	{
		$_websites = $this->_storeManager->getWebsites();
	    $_websiteData = array();
	    /*foreach($_websites as $website){
	    	echo "<pre>";
	    	print_r($website->debug()); die('tttt');
	        foreach($website->getStores() as $store){
	            $wedsiteId = $website->getId();
	            $storeObj = $this->_storeManager->getStore($store);
	            $name = $website->getName();
	            $url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
	            array_push($_websiteData, array('name' => $name,'url' => $url));
	        }
	    }*/

	    return $_websites;
	}

	public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getWebsite()->getId();
    }
}