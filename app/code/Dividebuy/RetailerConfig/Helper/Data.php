<?php

namespace Dividebuy\RetailerConfig\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_PRODUCT_ENABLED = 'dividebuy/product/enabled';
    const XML_PATH_PRODUCT_BANNER_IMAGE = 'dividebuy/product/banner_image';
    const DIVIDEBUY_MEDIA_DIR = 'dividebuy/';
    const XML_PATH_PRODUCT_CUSTOM_CSS = 'dividebuy/product/customcss';
    const XML_PATH_TOKEN_NUMBER = 'dividebuy/general/token_number';
    const XML_PATH_AUTH_NUMBER = 'dividebuy/general/auth_number';
    const XML_PATH_STORE_NAME = 'dividebuy/general/store_name';
    const XML_PATH_RETAILER_LOGO = 'dividebuy/general/retailer_image';
    const XML_PATH_PRODUCT_DVIDEBUY_ENABLE = 'dividebuy/general/product_dividebuy_default';

    const XML_PATH_RETAILER_ID = 'dividebuy/general/retailer_id';
    const DIVIDEBUY_INSTALMENTS = 'dividebuy/global/instalment_details';
    const DIVIDEBUY_CHECKOUT_BUTTON_IMAGE = 'payment/dbpayment/button_image';
    const XML_PATH_GOOGLE_ANALYTICS_UNIQUE_KEY = 'dividebuy/general/google_analytics_unique_id';
    const XML_PATH_DIVIDEBUY_EXTENSION_STATUS = 'dividebuy/general/extension_status';
    const XML_PATH_DIVIDEBUY_GLOBAL_DEACTIVATED = 'dividebuy/global/deactivated';
    const XML_PATH_DIVIDEBUY_ALLOWED_IP = 'dividebuy/general/allowed_ip';

    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    /**
     * Used to retrive storeID
     *
     * @return iteger
     */
    public function getStoreId()
    {
        if (!$this->_storeId) {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve current Product object
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve retailer id
     *
     * @return int
     */
    public function getRetailerId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RETAILER_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve store name
     *
     * @return string
     */
    public function getStoreName($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STORE_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve auth number
     *
     * @return string
     */
    public function getAuthNumber($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTH_NUMBER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * This function is used for getting list of allowed IPs stored in DivideBuy configuration.
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getAllowedIps($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DIVIDEBUY_ALLOWED_IP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve token number
     *
     * @return string
     */
    public function getTokenNumber($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TOKEN_NUMBER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product banner css
     *
     * @return string
     */
    public function getProductBannerCss($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_CUSTOM_CSS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product status
     *
     * @return int
     */
    public function getProductStatus($storeId = null)
    {
        $isIPAllowed = $this->isIPAllowed($storeId);
        $isDividebuyPaymentActive = $this->scopeConfig
            ->getValue(
                'payment/dbpayment/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        $productStatus = $this->scopeConfig
            ->getValue(
                self::XML_PATH_PRODUCT_ENABLED,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

        $extensionStatus = $this->scopeConfig
            ->getValue(
                self::XML_PATH_DIVIDEBUY_EXTENSION_STATUS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

        if ($productStatus && $isDividebuyPaymentActive && $isIPAllowed && $extensionStatus) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve retailer logo url
     *
     * @return string
     */
    public function getRetailerLogoUrl($storeId = null)
    {
        $retailerUrl = $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'dividebuy/' . $this->scopeConfig
            ->getValue('dividebuy/general/retailer_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $secureFlag = parse_url($retailerUrl, PHP_URL_SCHEME);
        if ($secureFlag != 'https') {
            $retailerUrl = $this->scopeConfig->getValue('dividebuy/global/logoUrl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
        }
        return $retailerUrl;
    }

    /**
     * Retrieve retailer logo
     *
     * @return string
     */
    protected function _getRetailerLogo($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RETAILER_LOGO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product banner url
     *
     * @return string
     */
    public function getProductBannerUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::DIVIDEBUY_MEDIA_DIR . $this->_getProductBanner();
    }

    /**
     * Retrieve product banner
     *
     * @return string
     */
    protected function _getProductBanner($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_BANNER_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve Checkout banner url
     *
     * @return string
     */
    public function getCheckoutBannerUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::DIVIDEBUY_MEDIA_DIR . $this->_getCheckoutBanner();
    }

    /**
     * Retrieve Checkout banner
     *
     * @return string
     */
    protected function _getCheckoutBanner($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::DIVIDEBUY_CHECKOUT_BUTTON_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Used to get instalment details from core config data
     *
     * @param int $totalPrice
     * @param int $storeId
     *
     * @return array
     */
    public function getInstalmentDetails($totalPrice, $storeId = null)
    {
        $instalments = unserialize($this->scopeConfig->getValue(self::DIVIDEBUY_INSTALMENTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId()));
        $instalmentDetails = array();
        $i = 0;
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();

        if ($totalPrice > 0) {
            foreach ($instalments as $instalment) {
                $instalmentPrice = $totalPrice / $instalment['key'];
                if ($totalPrice >= $instalment['value']) {
                    $instalmentDetails[$i]['months'] = $instalment['key'];
                    $instalmentDetails[$i]['value'] = $this->_priceCurrency->format($instalmentPrice, $includeContainer = true, 2, $scope = null, $currencyCode);
                    $instalmentDetails[$i]['available'] = 'yes';
                } else {
                    $instalmentDetails[$i]['months'] = $instalment['key'];
                    $instalmentDetails[$i]['value'] = $this->_priceCurrency->format($instalmentPrice, $includeContainer = true, 2, $scope = null, $currencyCode);
                    $instalmentDetails[$i]['available'] = 'no';
                }
                $i++;
            }
        }
        return $instalmentDetails;
    }

    /**
     * Used to check whether current ip is allowed to see Dividebuy functionality or not
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isIPAllowed($storeId = null)
    {
        $serverIP = $_SERVER['REMOTE_ADDR'];
        $allowedIP = $this->scopeConfig
            ->getValue(
                'dividebuy/general/allowed_ip',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        $allowedIPArray = array_map('trim', explode(',', $allowedIP));
        if (!empty($allowedIPArray) && $allowedIP != '') {
            if (in_array($serverIP, $allowedIPArray)) {
                $isIPAllowed = true;
            } else {
                $isIPAllowed = false;
            }
        } else {
            $isIPAllowed = true;
        }
        return $isIPAllowed;
    }

    public function isGuestCheckoutEnabled($storeId = null)
    {
        return $this->scopeConfig
                ->getValue(
                    'checkout/options/guest_checkout',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                );
    }

    /**
     * Get base URL
     *
     * @param string $scope
     *
     * @return string
     */
    public function getBaseUrl($scope = null)
    {
        return $this->_storeManager->getStore()->getBaseUrl($scope);
    }

    /**
     * Used to get google analytics unique key from configuration
     *
     * @param type $storeId
     *
     * @return string
     */
    public function getGoogleAnalyticUniqueKey($storeId = null)
    {
        return $this->scopeConfig
            ->getValue(
                self::XML_PATH_GOOGLE_ANALYTICS_UNIQUE_KEY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * Returns the configuration value of Set new products to shop with DivideBuy of Dividebuy configuration
     *
     * @param int $storeId
     *
     * @return int
     */
    public function getProductDividebuyEnableDefaultConfigValue($storeId = null)
    {
        return $this->scopeConfig
            ->getValue(
                self::XML_PATH_PRODUCT_DVIDEBUY_ENABLE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * Get the value of Activate/Deactivate Dividebuy field value from configuration
     *
     * @param int $storeId
     *
     * @return int
     */
    public function getExtensionStatus($storeId = null)
    {
        return $this->scopeConfig
            ->getValue(
                self::XML_PATH_DIVIDEBUY_EXTENSION_STATUS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }
}
