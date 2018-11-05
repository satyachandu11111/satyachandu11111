<?php

namespace Homescapes\Completelook\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_storeManagerInterface;

    public function __construct(
         \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
        )
    {
        $this->_storeManagerInterface = $storeManagerInterface;

    }

    public function DisplayDiscount($_product)
    {
        $originalPrice = $_product->getPrice();
        $finalPrice = $_product->getFinalPrice();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = number_format(($originalPrice - $finalPrice) * 100 / $originalPrice,0);
        }

        if ($percentage) {
            $storeId = $this->currentStoreId();
            if($storeId ==3 || $storeId ==2){  // fr and de percentage translate
                return '{-'.$percentage.'%}';
            }else{
                return '{'.$percentage.'% Off}';
            }
        }

    }


    public function currentStoreId()
    {
        $currentStore = $this->_storeManagerInterface->getStore();
        $currentStoreId = $currentStore->getId();

        return $currentStoreId;        
    }
}