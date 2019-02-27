<?php

namespace Homescapes\General\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
      protected $_productloader;  
      protected $helper;
      protected $order;
      protected $quoteFactory;
      protected $categoryFactory;
      private $currencyCode;     
      private $storeConfig;
      protected $_scopeConfig;
      protected $checkoutSession;



      public function __construct(
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Pricing\Helper\Data $helper,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Directory\Model\CurrencyFactory  $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession

    ) {

        $this->helper = $helper;
        $this->_productloader = $_productloader;
        $this->order = $order;
        $this->quoteFactory = $quoteFactory;
        $this->categoryFactory = $categoryFactory;
        $this->currencyCode = $currencyFactory->create();
        $this->storeConfig = $storeConfig;
        $this->_scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
    }

    public function isNew($product)
        {   
        
            
            if ($product->getData('news_from_date') == null && $product->getData('news_to_date') == null) {
                return false;
            }

            if ($product->getData('news_from_date') !== null) {
                if (date('Y-m-d', strtotime($product->getData('news_from_date'))) > date('Y-m-d', time())) {
                    return false;
                }
            }

            if ($product->getData('news_to_date') !== null) {
                if (date('Y-m-d', strtotime($product->getData('news_to_date'))) < date('Y-m-d', time())) {
                    return false;
                }
            }

            return true;
        }

        public function getpriceRange($_product)
        { 
            
            $_id=$_product->getId();
            //get associative (child) products
            if($_product->getTypeId() == "configurable")
            {
                $childProducts = $_product->getTypeInstance()->getUsedProducts($_product);
            }
            
            if($_product->getTypeId() == "grouped")
            {
             $childProducts = $_product->getTypeInstance()->getAssociatedProducts($_product);
            }
           
            $childPriceLowest = "";    
            $childPriceHighest = ""; 
            $childSpecialPriceLowest="";
           
            if(count($childProducts)>0){
                foreach($childProducts as $child){

                    $_child = $this->_productloader->create()->load($child->getId());
                    
                    if($childPriceLowest == '' || $childPriceLowest > $_child->getPrice() ){
                    $childPriceLowest =  $_child->getPrice();
                    $childSpecialPriceLowest =  $_child->getSpecialPrice();
                    }

                        if($childPriceHighest == '' || $childPriceHighest < $_child->getPrice() ){
                        $childPriceHighest =  $_child->getPrice();
                        $childSpecialPriceHighest =  $_child->getSpecialPrice();
                    }
                }  
                
                if($childSpecialPriceLowest>0)
                {
                    $html='<div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_id.'" data-price-box="product-id-'.$_id.'" ><p class="old-price"><span class="price" id="old-price-'.$_id.'">'.$this->helper->currency($childPriceLowest,true,false).' - '.$this->helper->currency($childPriceHighest,true,false).'</span></p><p class="special-price"><span class="price" id="product-price-'.$_id.'">'.$this->helper->currency($childSpecialPriceLowest,true,false).' - '.$this->helper->currency($childSpecialPriceHighest,true,false).'</span></p></div>';
                }
                else
                {
                    $html='<div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_id.'" data-price-box="product-id-'.$_id.'" ><p class="price"><span class="price-range" id="old-price-'.$_id.'">'.$this->helper->currency($childPriceLowest,true,false).' - '.$this->helper->currency($childPriceHighest,true,false).'</span></p></div>';
                }
                              
            }
            else
            {
                $childPricemain = $_product->getPrice();
                $childSpecialPricemain =  $_product->getSpecialPrice();
                if($childSpecialPricemain>0)
                {
                    $html='<div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_id.'" data-price-box="product-id-'.$_id.'" ><p class="old-price"><span class="price" id="old-price-'.$_id.'">'.$this->helper->currency($childPricemain,true,false).'</span></p><p class="special-price"><span class="price" id="product-price-'.$_id.'">'.$this->helper->currency($childSpecialPricemain,true,false).'</span></p></div>';
                }
                else
                {
                    $html='<div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_id.'" data-price-box="product-id-'.$_id.'" ><p class="old-price"><span class="price" id="old-price-'.$_id.'">'.$this->helper->currency($childPricemain,true,false).'</span></p><</div>';
                }
            }
            
            return $html;
        }

        public function loadOrderByIncrementById($incrementId)
        {
            $this->order->loadByIncrementId($incrementId);

            return $this->order;            
        }

        public function loadQuoteById($quoteId){            

            $quote = $this->quoteFactory->create()->load($quoteId);
            
            return $quote;
        }

        public function loadProduct($productId)
        {
            $product = $this->_productloader->create()->load($productId);

            return $product;
        }

        public function loadCategory($categoryId)
        {
            $category = $this->categoryFactory->create()->load($categoryId);

            return $category;
        }

        public function currentCurrencyCode()
        {
            $currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();

            return $currentCurrency;
        }

        public function getDisplayTimers(){
     
            return $this->_scopeConfig->getValue('homescapesGeneral/freeshipping/general_display_timer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);           
         
        }

        public function getFreeShippingTotal()
        {   
            return $this->_scopeConfig->getValue('homescapesGeneral/freeshipping/freeshipping_total', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);           
        }

        public function getFreeShippingText()
        {

            $result = array();

            $freeShippingTotal = $this->getFreeShippingTotal();
        
            $quote = $this->checkoutSession->getQuote();
            $subTotal = $quote->getSubtotal();

            $flag = $this->getCartItemIds($quote);
            
            $r_amount= 0;

            $currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();        

            $currency = $this->currencyCode->load($currentCurrency);
            $currencySymbol = $currency->getCurrencySymbol();
            

            $deliveryMessage = '';
            $remaingAmount = '';
            if(!$flag){

                if($subTotal >= $freeShippingTotal){
                    $deliveryMessage = 'you have qualified for free standard UK delivery on this order';
                }else{

                    $r_amount= $freeShippingTotal-$subTotal;
                    $remaingAmount = $currencySymbol.round($r_amount,2);

                    $deliveryMessage = 'Spend another <b>'.$remaingAmount.'</b> and you will qualify <b>for free delivery</b> on this order';
                }

            }


         $result['flag']  = $flag;
         $result['freeShippingTotal']  = $freeShippingTotal;
         $result['r_amount']  = $r_amount;
         $result['remainingAmount'] = $remaingAmount;
         return $result;   
            

        }

        public function getCartItemIds($quote)
    {
        $excludeCategories = $this->_scopeConfig->getValue('homescapesGeneral/excl/categories_ids', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); 

        $excludeCategoriesArray = explode(',',$excludeCategories);  
        $catIds = 0;

        $visibleItems = $quote->getAllVisibleItems();       
        
        foreach ($visibleItems as $item) {
               
                $cats = $item->getProduct()->getCategoryIds();
                //$logger->info($cats); 
                foreach ($cats as $category_id) {   
                    if (array_search($category_id, $excludeCategoriesArray))
                    {                       
                        $catIds = 1;
                        break;
                    }
                }            
            }

        
        return $catIds;
    }
}