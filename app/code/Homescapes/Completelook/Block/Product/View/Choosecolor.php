<?php

namespace Homescapes\Completelook\Block\Product\View;

class Choosecolor extends \Magento\Catalog\Block\Product\ProductList\Related
{
    
    protected $registry;
    
    protected $imageHelper;
    
    protected $productloader;      
    
    protected $scopeConfig;
    
    public function __construct(
            \Magento\Catalog\Block\Product\Context $context, 
            \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart, 
            \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, 
            \Magento\Checkout\Model\Session $checkoutSession, 
            \Magento\Framework\Module\Manager $moduleManager, 
            \Magento\Framework\Registry $registry,
            \Magento\Catalog\Helper\Image $imageHelper,
            \Magento\Catalog\Model\ProductFactory $_productloader,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            array $data = array()) {
        
        parent::__construct($context, $checkoutCart, $catalogProductVisibility, $checkoutSession, $moduleManager, $data);
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->productloader = $_productloader;
        $this->scopeConfig = $scopeConfig;
    }

    
    public function getCurrentProduct()
    {        
        return $this->registry->registry('current_product');
    }    
    
    public function getthumbnailImg($product){
        $imagewidth=50;
        $imageheight=50;
        return  $this->imageHelper->init($product, 'product_page_image_small')->setImageFile($product->getFile())->resize($imagewidth, $imageheight)->getUrl();
    }
    
    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }
    
    public function getDisplayText() {
        
        return $this->scopeConfig->getValue('completelook/general/display_text',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}