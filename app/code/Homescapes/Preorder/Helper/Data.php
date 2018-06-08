<?php

namespace Homescapes\Preorder\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $_productloader;  
    
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Catalog\Model\ProductFactory $_productloader
            ) {
         $this->_productloader = $_productloader;
        parent::__construct($context);
    }

    

    public function isPreOrder($product)
        {            
            //var_dump($product->getData('pre_order_from')); die('ddddd');
            
            if(!$product->getData('pre_order'))
                return false;
            
            if ($product->getData('pre_order_from') == null && $product->getData('pre_order_to') == null) {
                return false;
            }

            if ($product->getData('pre_order_from') !== null) {
                if (date('Y-m-d', strtotime($product->getData('pre_order_from'))) > date('Y-m-d', time())) {
                    
                    return false;
                }
            }

            if ($product->getData('pre_order_to') !== null) {
                if (date('Y-m-d', strtotime($product->getData('pre_order_to'))) < date('Y-m-d', time())) {
                    return false;
                }
            }

            return true;
        }
        
        public function isPreOrderCart($productId){
            
            $product = '';
            $product = $this->_productloader->create()->load($productId);
            
            if(!$product->getData('pre_order'))
                return false;
            
            if ($product->getData('pre_order_from') == null && $product->getData('pre_order_to') == null) {
                return false;
            }

            if ($product->getData('pre_order_from') !== null) {
                if (date('Y-m-d', strtotime($product->getData('pre_order_from'))) > date('Y-m-d', time())) {
                    
                    return false;
                }
            }

            if ($product->getData('pre_order_to') !== null) {
                if (date('Y-m-d', strtotime($product->getData('pre_order_to'))) < date('Y-m-d', time())) {
                    return false;
                }
            }

            return $product->getPreOrderNote();
            
            
        }
    
}
