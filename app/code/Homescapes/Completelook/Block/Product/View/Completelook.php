<?php

namespace Homescapes\Completelook\Block\Product\View;

class Completelook extends \Magento\Framework\View\Element\Template
{
    protected $resourceConnection;
    
    protected $registry;
    
    protected $productloader;  
    
    protected $imageHelper;
    
    protected $abstractProduct;
    
   /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
   protected $scopeConfig;


    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\App\ResourceConnection $resourceConnection,
            \Magento\Framework\Registry $registry,
            \Magento\Catalog\Model\ProductFactory $_productloader,
            \Magento\Catalog\Helper\Image $imageHelper,
            \Magento\Catalog\Block\Product\AbstractProduct $abstractProduct,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            array $data = array()
            ) {            
            $this->resourceConnection = $resourceConnection;
            $this->registry = $registry;            
            $this->productloader = $_productloader;
            $this->imageHelper = $imageHelper;
            $this->abstractProduct = $abstractProduct;
            $this->scopeConfig = $scopeConfig;
            parent::__construct($context, $data);
        }
    
    public function getCurrentProduct()
    {        
        return $this->registry->registry('current_product');
    }

    
    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }
        
    public function getCompletelookProducts(){
        
        $currentProductId = $this->getCurrentProduct()->getId();
        
        if(!$currentProductId){
            return false;
        }
        
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        $sql = $connection->select()
                  ->from($tableName,array('look_product_id'))                  
                  ->where('product_id = ?', $currentProductId)
                  ->order("position ASC");       
        $results = $connection->fetchAll($sql); 
        
        if(count($results)){
            foreach($results as $result){
                $products[] = $result['look_product_id'];
            }    
        }else{
            $products = '';
        }           
        return $products;
    }
    
    
    public function getAssociatedProducts($product)
    {
        return $product->getTypeInstance()->getAssociatedProducts($product);
    }
    
    public function getImageUrl($_product)
    {
        $image = $this->imageHelper->init($_product,'category_page_list')->constrainOnly(FALSE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(250)->getUrl();
        return $image;
    }   
    
    public function getProductPrice($product)
    {
        return $this->abstractProduct->getProductPrice($product);
    }
    
    public function getCanShowProductPrice($product)
    {
        return $this->abstractProduct->getCanShowProductPrice($product);
    }
    
    public function getSubmitUrl($product){
        return $this->abstractProduct->getSubmitUrl($product);
    }
    
    public function getDisplayText() {

        return $this->scopeConfig->getValue('completelook/general/display_text',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
}
