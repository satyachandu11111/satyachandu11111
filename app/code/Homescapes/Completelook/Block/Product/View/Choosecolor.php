<?php

namespace Homescapes\Completelook\Block\Product\View;

class Choosecolor extends \Magento\Catalog\Block\Product\ProductList\Related
{
    
    protected $registry;
    
    protected $imageHelper;
    
    protected $productloader;  
    
    public function __construct(
            \Magento\Catalog\Block\Product\Context $context, 
            \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart, 
            \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, 
            \Magento\Checkout\Model\Session $checkoutSession, 
            \Magento\Framework\Module\Manager $moduleManager, 
            \Magento\Framework\Registry $registry,
            \Magento\Catalog\Helper\Image $imageHelper,
            \Magento\Catalog\Model\ProductFactory $_productloader,
            array $data = array()) {
        
        parent::__construct($context, $checkoutCart, $catalogProductVisibility, $checkoutSession, $moduleManager, $data);
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->productloader = $_productloader;
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
    
}