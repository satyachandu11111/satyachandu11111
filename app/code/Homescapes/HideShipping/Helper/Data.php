<?php

namespace Homescapes\HideShipping\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Hide Shipping Helper
 */
class Data extends AbstractHelper
{
    /**
     * Enabled Config Path
     */
    const XML_CONFIG_ENABLED = 'hideshipping/general/enable';

    /**
     * Check Hide Shipping mode Functionality Should be Enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_getConfig(self::XML_CONFIG_ENABLED);
    }

    /**
     * Retrieve Store Configuration Data
     *
     * @param   string $path
     * @return  string|null
     */
    protected function _getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}