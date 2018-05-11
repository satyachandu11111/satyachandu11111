<?php

namespace Homescapes\General\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

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
}