<?php

namespace Homescapes\Completelook\Helper;    

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function DisplayDiscount($_product)
    {
        $originalPrice = $_product->getPrice();
        $finalPrice = $_product->getFinalPrice();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = number_format(($originalPrice - $finalPrice) * 100 / $originalPrice,0);
        }

        if ($percentage) {
            return '{'.$percentage.'% Off}';
        }

    }
}