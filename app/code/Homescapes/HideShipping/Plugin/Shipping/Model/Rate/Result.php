<?php

namespace Homescapes\HideShipping\Plugin\Shipping\Model\Rate;

use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Rate\Result as Subject;
use Homescapes\HideShipping\Helper\Data as HideShippingHelper;

/**
 * Shipping Result Plugin
 */
class Result
{
    
    protected $helper;

    
    public function __construct(
        HideShippingHelper $helper
    ) {
        $this->helper = $helper;
    }

    
    public function afterGetAllRates(Subject $subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }       
        
        $rates = $this->getAllFreeRates($result);
        return (count($rates) > 0) ? $rates : $result;
    }

   
    public function getAllFreeRates($result)
    {
        $flag = false;
        $count=0;
        $flatRatePosition = 0;

        $rates = [];
            foreach ($result ?: [] as $rate) {          
            
                if ($rate->getCarrier() == 'freeshipping') {
                    $flag=true;
                }
                if ($rate->getCarrier() == 'flatrate') {
                    $flatRatePosition=$count;
                }
                $rates[] = $rate;
                $count++;
            }

            if($flag){
                unset($rates[$flatRatePosition]); 
            }
        
        return $rates;
    }
}