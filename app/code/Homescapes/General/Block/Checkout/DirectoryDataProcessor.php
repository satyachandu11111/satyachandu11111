<?php
namespace Homescapes\General\Block\Checkout;

class DirectoryDataProcessor 
{
    protected $_cart;
    
    protected $_productloader;  
    
    public function __construct(
            \Magento\Checkout\Model\Cart $cart,
            \Magento\Catalog\Model\ProductFactory $_productloader)
    {
        $this->_cart = $cart;
        $this->_productloader = $_productloader;
    }
    
    public function afterProcess(\Magento\Checkout\Block\Checkout\DirectoryDataProcessor $subject, $result) {
        
            
            
            $shipInUkOnly = $this->checkShipItems();
            if($shipInUkOnly){
                $result['components']['checkoutProvider']['dictionaries']['country_id'] = Array(array('value' => '','label' => ''),array('value' => 'GB','label' => 'United Kingdom','is_region_visible'=>1, 'is_zipcode_optional' => 1));
            }
            return $result; 
        }
    
    public function checkShipItems(){
        
            $flag = 0;
            $itemsVisible = $this->_cart->getQuote()->getAllVisibleItems();
            
            foreach($itemsVisible as $item) {
                
                $_product = $this->_productloader->create()->load($item->getProductId());
                if($_product->getShippingInUk()){
                    $flag = 1;
                    break;
                }                
                
             }
             
            return $flag;
        }
}
