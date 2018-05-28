<?php
namespace Homescapes\Orderswatch\Block;

class Swatchlist extends \Magento\Framework\View\Element\Template
{
    
    protected $imageHelper;
    
    protected $coreSession;
    
    protected $productloader;  
    
    protected $scopeConfig;   
    
    protected $countryCollectionFactory;
    
    
    public function __construct(
            \Magento\Catalog\Block\Product\Context $context, 
            \Magento\Catalog\Helper\Image $imageHelper,
            \Magento\Framework\Session\SessionManagerInterface $coreSession,
            \Magento\Catalog\Model\ProductFactory $_productloader,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Directory\Model\Config\Source\Country $countryCollectionFactory,
            array $data = array()) 
    {        
        parent::__construct($context, $data);        
        $this->coreSession = $coreSession;
        $this->imageHelper = $imageHelper;
        $this->productloader = $_productloader;    
        $this->scopeConfig = $scopeConfig;
        $this->countryCollectionFactory = $countryCollectionFactory;
        
    }
    
    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }
    
    public function getsmallImg($product)
    {
        $imagewidth=50;
        $imageheight=50;
        return  $this->imageHelper->init($product, 'product_page_image_small')->setImageFile($product->getFile())->resize($imagewidth, $imageheight)->getUrl();
    }

    
    public function setHomescapessampleswatchclose($value)
    {
                $this->coreSession->start();    
        return $this->coreSession->setHomescapessampleswatchclose($value); //set value in customer session
    }

    public function getHomescapessampleswatchclose()
    {
        $this->coreSession->start();    
        return $this->coreSession->getHomescapessampleswatchclose(); //Get value from customer session
    }
    
    
    public function setHomescapessampleswatch($value)
    {
        $this->coreSession->start();    
        return $this->coreSession->setHomescapessampleswatch($value); //set value in customer session
    }

    public function getHomescapessampleswatch()
    {
        $this->coreSession->start();    
        return $this->coreSession->getHomescapessampleswatch(); //Get value from customer session
    }
    
    public function getShipCountry()
    {
        return $this->_scopeConfig->getValue('orderswatch/general/specificcountry', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getCountryCollection()
    {
        return $collection = $this->countryCollectionFactory->toOptionArray();
    }
    
    
    public function setLastswatchId($value)
    {
        $this->coreSession->start();    
        return $this->coreSession->setLastswatchId($value); //set value in customer session
    }

    public function getLastswatchId()
    {
        $this->coreSession->start();    
        return $this->coreSession->getLastswatchId(); //Get value from customer session
    }
     
}

