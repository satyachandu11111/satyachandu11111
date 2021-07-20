<?php
namespace Homescapes\Pinteresttag\Block\Checkout;

class CartPinteresttag extends \Magento\Framework\View\Element\Template
{
    protected $_cart;
    
    protected $_productloader;

    protected $_storeManager;
    protected $_generalHelper;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
            \Magento\Checkout\Model\Cart $cart,
            \Homescapes\General\Helper\Data $generalHelper,
            array $data = []
            )
    {
        $this->_cart = $cart;
        $this->_generalHelper = $generalHelper;
         parent::__construct(
            $context,
            $data
        );
        
    }

     /**
     * Preparing layout
     *
     * @return \Magento\Catalog\Block\Breadcrumbs
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getCartItems()
    {
        
            $items =[];
            $itemsVisible = $this->_cart->getQuote()->getAllVisibleItems();
            
            foreach($itemsVisible as $item) {
                $catName=$this->getCategoryName($item->getProductId());
                $items[]=['product_id'=>$item->getProductId(),'product_name'=> $item->getName(),'product_sku'=>$item->getSku(),'product_price'=>$item->getPrice(),'category_name'=>$catName];
                
             }
             
            return $items;
    }

    public function getCurrentOrderDetails()
    {
            return $quote = $this->_cart->getQuote();
            
    }

    public function getCategoryName($productId){

        $productidd = $this->_generalHelper->loadProduct($productId);                
        $cats = $productidd->getCategoryIds();
        $firstCategoryId = $cats[0];
        
        $category = $this->_generalHelper->loadCategory($firstCategoryId);
        return $categoryName = $category->getName();                
    }
}
