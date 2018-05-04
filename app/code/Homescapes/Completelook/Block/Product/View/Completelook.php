<?php

namespace Homescapes\Completelook\Block\Product\View;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Completelook extends \Magento\Framework\View\Element\Template
{
    protected $resourceConnection;
    
    protected $registry;
    
    protected $productloader;  
    
    protected $imageHelper;
    
    protected $abstractProduct;
       
    protected $scopeConfig;
    
    protected $arrayUtils;
    
    protected $helper;
    
    protected $configurableAttributeData;
    
    protected $jsonEncoder;
    
    private $_localeFormat;
    
    protected $priceCurrency;
    
    protected $productTypeConfig;
    
    protected $cartHelper;


    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\App\ResourceConnection $resourceConnection,
            \Magento\Framework\Registry $registry,
            \Magento\Catalog\Model\ProductFactory $_productloader,
            \Magento\Catalog\Helper\Image $imageHelper,
            \Magento\Catalog\Block\Product\AbstractProduct $abstractProduct,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
            \Magento\ConfigurableProduct\Helper\Data $helper,
            \Magento\Catalog\Helper\Product $catalogProduct,
            \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
            \Magento\Framework\Json\EncoderInterface $jsonEncoder,
            PriceCurrencyInterface $priceCurrency,
            \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
            \Magento\Checkout\Helper\Cart  $cartHelper,
            array $data = array(),
            Format $localeFormat = null
            ) {            
            $this->resourceConnection = $resourceConnection;
            $this->registry = $registry;            
            $this->productloader = $_productloader;
            $this->imageHelper = $imageHelper;
            $this->abstractProduct = $abstractProduct;
            $this->scopeConfig = $scopeConfig;
            $this->arrayUtils = $arrayUtils;
            $this->helper = $helper;
            $this->catalogProduct = $catalogProduct;
            $this->configurableAttributeData = $configurableAttributeData;
            $this->jsonEncoder = $jsonEncoder;
            $this->priceCurrency = $priceCurrency;
            $this->_localeFormat = $localeFormat ?: ObjectManager::getInstance()->get(Format::class);
            $this->productTypeConfig = $productTypeConfig;
            $this->cartHelper = $cartHelper;
            parent::__construct($context, $data);
        }
    
    public function getCurrentProduct()
    {        
        return $this->registry->registry('current_product');
    }

    
    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }
        
    public function getCompletelookProducts(){
        
        $currentProductId = $this->getCurrentProduct()->getId();
        
        if(!$currentProductId){
            return false;
        }
        
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(\Homescapes\Completelook\Model\Completelook::COMPLETE_LOOK_PRODUCT);
        $sql = $connection->select()
                  ->from($tableName,array('look_product_id'))                  
                  ->where('product_id = ?', $currentProductId)
                  ->order("position ASC");       
        $results = $connection->fetchAll($sql); 
        
        if(count($results)){
            foreach($results as $result){
                $products[] = $result['look_product_id'];
            }    
        }else{
            $products = '';
        }           
        return $products;
    }
    
    
    public function getAssociatedProducts($product)
    {
        return $product->getTypeInstance()->getAssociatedProducts($product);
    }
    
    public function getImageUrl($_product)
    {
        $image = $this->imageHelper->init($_product,'category_page_list')->constrainOnly(FALSE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(250)->getUrl();
        return $image;
    }   
    
    public function getProductPrice($product)
    {
        return $this->abstractProduct->getProductPrice($product);
    }
    
    public function getCanShowProductPrice($product)
    {
        return $this->abstractProduct->getCanShowProductPrice($product);
    }
    
    public function getSubmitUrl($product){
        return $this->abstractProduct->getSubmitUrl($product);
    }   
    
    public function getAddToCartUrlForProduct($product)
    {
        return $this->cartHelper->getAddUrl($product);
    }
    
    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }
    
    
    /**
     * Decorate a plain array of arrays or objects
     *
     * @param array $array
     * @param string $prefix
     * @param bool $forceSetAll
     * @return array
     */
    public function decorateArray($array, $prefix = 'decorated_', $forceSetAll = false)
    {
        return $this->arrayUtils->decorateArray($array, $prefix, $forceSetAll);
    }
    
    
    public function getAllowProductsForList($_product)
    {
        //if (!$this->hasAllowProductsForList()) {
            $products = [];
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
            $allProducts = $_product->getTypeInstance()->getUsedProducts($_product, null);
            
            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                    
                    
                }
            }
            
            $this->setAllowProductsForList($products);
        //}
        return $this->getData('allow_products_for_list');
    }
    
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }
    
     public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }
    
    protected function _getAdditionalConfig()
    {
        return [];
    }
    
    public function shouldRenderQuantity($product)
    {
        return !$this->productTypeConfig->isProductSet($product->getTypeId());
    }
    
    public function hasOptions($product)
    {
        $attributes = $this->getAllowAttributes($product);
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
                if ($attribute->getData('options')) {
                    return true;
                }
            }
        }
        return false;
    }
    
    protected function getOptionPrices($cproduct)
    {
        $prices = [];
        foreach ($this->getAllowProductsForList($cproduct) as $product) {
            $tierPrices = [];
            $priceInfo = $product->getPriceInfo();
            $tierPriceModel =  $priceInfo->getPrice('tier_price');
            $tierPricesList = $tierPriceModel->getTierPriceList();
            foreach ($tierPricesList as $tierPrice) {
                $tierPrices[] = [
                    'qty' => $this->localeFormat->getNumber($tierPrice['price_qty']),
                    'price' => $this->localeFormat->getNumber($tierPrice['price']->getValue()),
                    'percentage' => $this->localeFormat->getNumber(
                        $tierPriceModel->getSavePercent($tierPrice['price'])
                    ),
                ];
            }

            $prices[$product->getId()] =
                [
                    'oldPrice' => [
                        'amount' => $this->_localeFormat->getNumber(
                            $priceInfo->getPrice('regular_price')->getAmount()->getValue()
                        ),
                    ],
                    'basePrice' => [
                        'amount' => $this->_localeFormat->getNumber(
                            $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount()
                        ),
                    ],
                    'finalPrice' => [
                        'amount' => $this->_localeFormat->getNumber(
                            $priceInfo->getPrice('final_price')->getAmount()->getValue()
                        ),
                    ],
                    'tierPrices' => $tierPrices,
                 ];
        }
        return $prices;
    }
    
    public function getJsonConfigCompletelook($product)
  {

    $store = $this->getCurrentStore();
    $currentProduct = $product;
    $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
    $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');
//    if($configProdOptFrontend == 'matrix'){
//        $options = $this->helper->getOptionsForMatrix($currentProduct, $this->getAllowProductsForProdList($product));
//    }else{
//         $options = $this->helper->getOptions($currentProduct, $this->getAllowProductsForProdList($product));
//    }
    $options = $this->helper->getOptions($currentProduct, $this->getAllowProductsForList($product));
    $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);
    
    $config = [
        'attributes' => $attributesData['attributes'],
        'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
        'currencyFormat' => $store->getCurrentCurrency()->getOutputFormat(),
        'optionPrices' => $this->getOptionPrices($product),
        'priceFormat' => $this->_localeFormat->getPriceFormat(),
        'currencySymbol'=>str_replace('%s','', $store->getCurrentCurrency()->getOutputFormat()),
        'prices' => [
                'oldPrice' => [
                    'amount' => $this->_localeFormat->getNumber($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_localeFormat->getNumber($finalPrice->getAmount()->getBaseAmount()),
                ],
                'finalPrice' => [
                    'amount' => $this->_localeFormat->getNumber($finalPrice->getAmount()->getValue()),
                ],
            ],
        'productId' => $currentProduct->getId(),
        'chooseText' => __('Choose an Option ...'),
        'images' => isset($options['images']) ? $options['images'] : [],
        'index' => isset($options['index']) ? $options['index'] : [],
        'containerId' => $currentProduct->getId() ? '.configurable-container-'.$currentProduct->getId() : '',
    ];

    if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
        $config['defaultValues'] = $attributesData['defaultValues'];
    }

    $config = array_merge($config, $this->_getAdditionalConfig());
    

    return $this->jsonEncoder->encode($config);
}

        public function getPriceJsonConfig($product)
        {
            
            //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//            $this->priceCurrency = $objectManager->create('Magento\Framework\Pricing\PriceCurrencyInterface');
//            $this->_localeFormat = $objectManager->create('Magento\Framework\Locale\FormatInterface');
            /* @var $product \Magento\Catalog\Model\Product */
            

            if (!$this->hasOptions($product)) {
                $config = [
                    'productId' => $product->getId(),
                    'priceFormat' => $this->_localeFormat->getPriceFormat(),
                    ];

                return $this->jsonEncoder->encode($config);
            }

            $tierPrices = [];
            $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
            foreach ($tierPricesList as $tierPrice) {
                $tierPrices[] = $this->priceCurrency->convert($tierPrice['price']->getValue());
            }
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat(),
                'prices' => [
                    'oldPrice' => [
                        'amount' => $this->priceCurrency->convert(
                            $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue()
                        ),
                        'adjustments' => [],
                    ],
                    'basePrice' => [
                        'amount' => $this->priceCurrency->convert(
                            $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount()
                        ),
                        'adjustments' => [],
                    ],
                    'finalPrice' => [
                        'amount' => $this->priceCurrency->convert(
                            $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()
                        ),
                        'adjustments' => [],
                    ],
                ],
                'idSuffix' => '_clone',
                'tierPrices' => $tierPrices,
            ];

            $responseObject = new \Magento\Framework\DataObject();
            $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
            if (is_array($responseObject->getAdditionalOptions())) {
                foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                    $config[$option] = $value;
                }
            }

            return $this->jsonEncoder->encode($config);
        }
    
}
