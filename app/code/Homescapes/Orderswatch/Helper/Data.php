<?php

namespace Homescapes\Orderswatch\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $productloader;  
    
    protected $imageHelper;
    
    protected $_scopeConfig;
    
    protected $coreSession;
    
    public function __construct(\Magento\Framework\App\Helper\Context $context,
            \Magento\Catalog\Model\ProductFactory $_productloader,
            \Magento\Catalog\Helper\Image $imageHelper,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Framework\Session\SessionManagerInterface $coreSession) {
        parent::__construct($context);
        $this->productloader = $_productloader;
        $this->imageHelper = $imageHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->coreSession = $coreSession;
    }

    public function getLoadProduct($id)
    {
        $product = $this->productloader->create()->load($id);     
        return $product;
    }
    
    public function getsmallImg($product)
    {
        $imagewidth=50;
        $imageheight=50;
        return  $this->imageHelper->init($product, 'product_page_image_small')->setImageFile($product->getFile())->resize($imagewidth, $imageheight)->getUrl();
    }
    
     public function getSystemValues($path){
     
       return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
     
    }
    
    
    public function setLastswatchId($value)
    {
        $this->coreSession->start();    
        return $this->coreSession->setLastswatchId($value); //set value in customer session
    }

    public function getLastswatchId()
    {
        $this->coreSession->start();    
        return $this->coreSession->getLastswatchId(); //Get value from customer session
    }
    
     public function setHomescapessampleswatch($value)
    {
        $this->coreSession->start();    
        return $this->coreSession->setHomescapessampleswatch($value); //set value in customer session
    }

    public function getHomescapessampleswatch()
    {
        $this->coreSession->start();    
        return $this->coreSession->getHomescapessampleswatch(); //Get value from customer session
    }
}
