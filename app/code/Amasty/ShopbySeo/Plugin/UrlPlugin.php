<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Plugin;

use Amasty\ShopbySeo\Helper\Url;
use Magento\Framework\UrlInterface;

class UrlPlugin
{
    /**
     * @var  Url
     */
    protected $helper;

    /** @var  string|null */
    private $baseHost = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $brandHelper;

    public function __construct(
        Url $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->brandHelper = $brandHelper;
    }

    /**
     * @param UrlInterface $subject
     * @param $native
     * @return string
     */
    public function afterGetUrl(UrlInterface $subject, $native)
    {
        $isBrandOrSeoEnable = (strpos($native, $this->brandHelper->getBrandAttributeCode()) !== false
            || $this->helper->isSeoUrlEnabled());
        if ($isBrandOrSeoEnable
            && $this->_isInternalHost($native)
            && $this->helper->getRequest()->getFullActionName() != 'catalogsearch_result_index'
        ) {
            return $this->helper->seofyUrl($native);
        } else {
            return $native;
        }
    }

    /**
     * @param string $native
     * @return bool
     */
    protected function _isInternalHost($native)
    {
        if ($this->baseHost === null) {
            $currentBaseUrl = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
            $this->baseHost = parse_url($currentBaseUrl, PHP_URL_HOST);
        }
        $nativeHost = parse_url($native, PHP_URL_HOST);
        return !strcasecmp($this->baseHost, $nativeHost);
    }
}
