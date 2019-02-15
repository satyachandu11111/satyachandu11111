<?php

namespace Homescapes\General\Plugin;



class CustomerData {


	protected $_scopeConfig;

	protected $checkoutSession;

	private $storeConfig;

	private $currencyCode;

	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeConfig,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
    	$this->checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->storeConfig = $storeConfig;
        $this->currencyCode = $currencyFactory->create();
    }    

    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        // Get the previous data
        $data = $result;


		$freeShippingTotal = $this->_scopeConfig->getValue('homescapesGeneral/freeshipping/freeshipping_total', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
		$quote = $this->checkoutSession->getQuote();
		$subTotal = $quote->getSubtotal();

		
		$flag = $this->getCartItemIds($quote);

		$r_amount= 0;

		$deliveryMessage = '';
		if(!$flag){

			if($subTotal >= $freeShippingTotal){
				$deliveryMessage = 'you have qualified for free standard UK delivery on this order';
			}else{

				$r_amount= $freeShippingTotal-$subTotal;

				$deliveryMessage = 'Spend another <b>'.$r_amount.'</b> and you will qualify <b>for free delivery</b> on this order';
			}

		}



		$currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();		
        $currency = $this->currencyCode->load($currentCurrency);
        $currencySymbol = $currency->getCurrencySymbol();
        $remaingAmount = $currencySymbol.round($r_amount,2);
        

        // Append variable        
        $data['freeshippingtext'] = $deliveryMessage;
        $data['freeshippingamount'] = $r_amount;
        $data['freeshippingramount'] = $remaingAmount;
        $data['freeshippingflag'] = $flag;
        return $data;
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