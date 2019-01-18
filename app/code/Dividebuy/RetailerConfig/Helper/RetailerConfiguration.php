<?php
namespace Dividebuy\RetailerConfig\Helper;

class RetailerConfiguration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DIVIDEBUY_API_URL_STAGING       = 'http://192.192.11.1/dev_laravel/public/';
    const DIVIDEBUY_ORDER_URL_STAGING     = '192.192.11.36/#/login';
    const DIVIDEBUY_PORTAL_URL_STAGING    = 'https://portal.dividebuysandbox.co.uk/?adminToken=';
    const DIVIDEBUY_API_URL_PRODUCTION    = 'http://192.192.11.1/dev_laravel/public/';
    const DIVIDEBUY_ORDER_URL_PRODUCTION  = '192.192.11.36/#/login';
    const DIVIDEBUY_PORTAL_URL_PRODUCTION = 'https://portal.dividebuy.co.uk/?adminToken=';

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function getApiUrl($storeId)
    {
        $environment     = $this->_scopeConfig->getValue('dividebuy/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if ($environment == "staging") {
            return self::DIVIDEBUY_API_URL_STAGING;
        } else {
            return self::DIVIDEBUY_API_URL_PRODUCTION;
        }
    }

    public function getPortalUrl($storeId)
    {
        $environment     = $this->_scopeConfig->getValue('dividebuy/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if ($environment == "staging") {
            return self::DIVIDEBUY_PORTAL_URL_STAGING;
        } else {
            return self::DIVIDEBUY_PORTAL_URL_PRODUCTION;
        }
    }

    public function getOrderUrl($storeId)
    {
        $environment = $this->_scopeConfig->getValue('dividebuy/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

        $domainUrlPrefix = $this->_scopeConfig->getValue('dividebuy/general/domain_url_prefix', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

        if ($environment == "staging") {
            return "https://" .$domainUrlPrefix. "." .self::DIVIDEBUY_ORDER_URL_STAGING;
        } else {
            return "https://" .$domainUrlPrefix. "." .self::DIVIDEBUY_ORDER_URL_PRODUCTION;
        }
    }
}
