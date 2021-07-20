<?php
 
namespace Homescapes\HubBox\Model\Rewrite;
 
use HubBox\HubBox\Model\Collectable as HubBoxCollectable;
 
class Collectable extends HubBoxCollectable
{
    public function isCollectable()
    {
    	$visibleItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
 
 		$weightSum = 0;

 		 		
	    foreach ($visibleItems as $item)
	    {
	        if($item->getProduct()->getWeight())
	        {
	        	
	        	$weightSum += ($item->getProduct()->getWeight()* $item->getQty()); 
	    		
	        }
	    }


	    if($weightSum > 20){
	    	return false;
	    }else{
	    	return true;
	    }
	 
    }
}


