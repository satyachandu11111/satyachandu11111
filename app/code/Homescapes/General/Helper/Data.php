<?php

namespace Homescapes\General\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
      protected $_productloader;  
      protected $helper;

      public function __construct(
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Pricing\Helper\Data $helper

    ) {

        $this->helper = $helper;

        $this->_productloader = $_productloader;
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
                    $html='<div class="price-box"><p class="old-price"><span class="price" id="old-price-'.$_id.'">'.$this->helper->currency($childPriceLowest,true,false).' - '.$this->helper->currency($childPriceHighest,true,false).'</span></p><p class="special-price"><span class="price" id="product-price-'.$_id.'">'.$this->helper->currency($childSpecialPriceLowest,true,false).' - '.$this->helper->currency($childSpecialPriceHighest,true,false).'</span></p></div>';
                }
                else
                {
                    $html='<div class="price-box"><p class="old-price"><span class="price-range" id="old-price-'.$_id.'">'.$this->helper->currency($childPriceLowest,true,false).' - '.$this->helper->currency($childPriceHighest,true,false).'</span></p></div>';
                }
                              
            }
            else
            {
                $childPricemain = $_product->getPrice();
                $childSpecialPricemain =  $_product->getSpecialPrice();
                if($childSpecialPricemain>0)
                {
                    $html='<div class="price-box"><p class="old-price"><span class="price" id="old-price-'.$_id.'">'.$this->helper->currency($childPricemain,true,false).'</span></p><p class="special-price"><span class="price" id="product-price-'.$_id.'">'.$this->helper->currency($childSpecialPricemain,true,false).'</span></p></div>';
                }
                else
                {
                    $html='<div class="price-box"><p class="old-price"><span class="price" id="old-price-'.$_id.'">'.$this->helper->currency($childPricemain,true,false).'</span></p><</div>';
                }
            }
            
            return $html;
        }
}