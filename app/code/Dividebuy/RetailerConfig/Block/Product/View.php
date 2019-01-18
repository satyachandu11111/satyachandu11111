<?php
namespace Dividebuy\RetailerConfig\Block\Product;

use Dividebuy\RetailerConfig\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_helper;

    /**
     * @param Context $context
     * @param Data $retailerHelper
     * @param array $data
     */
    public function __construct(Context $context, Data $retailerHelper, array $data = [])
    {
        $this->_helper = $retailerHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check that the product is dividebuy or not.If it is dividebuy, then it returns the array of required attribute
     *
     * @return array
     */
    public function checkDivideBuy()
    {
        $storeId = $this->_helper->getStoreId();
        if ($this->_helper->getProductStatus($storeId) && $this->_helper->getProduct()->getDividebuyEnable()) {
            return array(
                "status"           => $this->_helper->getProductStatus($storeId),
                "banner_image"     => $this->_helper->getProductBannerUrl($storeId),
                "custom_css"       => $this->_helper->getProductBannerCss($storeId),
                "dividebuy_enable" => $this->_helper->getProduct()->getDividebuyEnable(),
            );
        }
        return false;
    }
}
