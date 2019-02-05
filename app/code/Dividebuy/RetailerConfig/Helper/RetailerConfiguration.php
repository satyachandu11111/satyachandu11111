<?php
namespace Dividebuy\RetailerConfig\Helper;

class RetailerConfiguration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DIVIDEBUY_API_URL_STAGING       = 'https://api.dividebuysandbox.co.uk/';
    const DIVIDEBUY_ORDER_URL_STAGING     = 'dividebuysandbox.co.uk/#/login';
    const DIVIDEBUY_PORTAL_URL_STAGING    = 'https://portal.dividebuysandbox.co.uk/?adminToken=';
    const DIVIDEBUY_API_URL_PRODUCTION    = 'https://api.dividebuy.co.uk/';
    const DIVIDEBUY_ORDER_URL_PRODUCTION  = 'dividebuy.co.uk/#/login';
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
