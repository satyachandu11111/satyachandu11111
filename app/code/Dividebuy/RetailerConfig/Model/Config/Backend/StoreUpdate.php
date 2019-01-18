<?php

namespace Dividebuy\RetailerConfig\Model\Config\Backend;

class StoreUpdate extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendModelSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Backend\Model\Session                               $backendModelSession
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $config
     * @param \Magento\Framework\App\Cache\TypeListInterface               $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Session $backendModelSession,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_backendModelSession = $backendModelSession;
        $this->_storeManager        = $storeManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
    	$storeId = $this->_storeManager->getStore()->getId();

    	$checkoutButtonImage = $this->_config->getValue(
            \Dividebuy\CheckoutConfig\Helper\Data::XML_PATH_CART_BUTTON_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $checkoutButtonHoverImage = $this->_config->getValue(
            \Dividebuy\CheckoutConfig\Helper\Data::XML_PATH_CART_BUTTON_HOVER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $retailerImage = $this->_config->getValue("dividebuy/general/retailer_image",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $productBannerImage = $this->_config->getValue("dividebuy/product/banner_image",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $extensionStatus = $this->_config->getValue(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_EXTENSION_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $this->_backendModelSession->setPreviousRetailerImage($retailerImage);
        $this->_backendModelSession->setPreviousProductBannerImage($productBannerImage);
        $this->_backendModelSession->setPreviousCheckoutButtonHoverImage($checkoutButtonHoverImage);
        $this->_backendModelSession->setPreviousCheckoutButtonImage($checkoutButtonImage);
        $this->_backendModelSession->setPreviousExtensionStatus($extensionStatus);
        parent::beforeSave();
    }
}
