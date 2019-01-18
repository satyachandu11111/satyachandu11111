<?php

namespace Dividebuy\RetailerConfig\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;

class ConfigObserver implements ObserverInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendModelSession;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileManagement;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_eavAttribute;

    /**
     * @var \Dividebuy\CheckoutConfig\Helper\Api
     */
    protected $_checkoutConfigApiHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Logger $logger
     * @param \Magento\Backend\Model\Session $backendModelSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Dividebuy\RetailerConfig\Helper\Data $retailerHelper
     * @param \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
     */
    public function __construct(Logger $logger, \Magento\Backend\Model\Session $backendModelSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Json\Helper\Data $jsonHelper, \Dividebuy\RetailerConfig\Helper\Data $retailerHelper, \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper, \Magento\Framework\Filesystem\Driver\File $file, \Magento\Framework\Filesystem $fileSystem, \Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Eav\Model\Entity\Attribute $eavAttribute,
        \Dividebuy\CheckoutConfig\Helper\Api $checkoutConfigApiHelper,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger,
        \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->logger = $logger;
        $this->_backendModelSession = $backendModelSession;
        $this->storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->_helper = $retailerHelper;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
        $this->_fileManagement = $file;
        $this->_fileSystem = $fileSystem;
        $this->_config = $config;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutConfigApiHelper = $checkoutConfigApiHelper;
        $this->_divideBuylogger = $divideBuylogger;
        $this->_eavAttribute = $eavAttribute;
        $this->messageManager = $messageManager;
    }

    /**
     * updateRetailerStoreCode in dividebuy
     *
     * @param EventObserver $observer
     *
     * @return mixed
     */
    public function execute(EventObserver $observer)
    {
        $storeCode = $observer->getStore();
        $storeId = $this->storeManager->getStore()->getId();
        $dividebuyMediaDirectory = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() . \Dividebuy\CheckoutConfig\Helper\Data::DIVIDEBUY_MEDIA_DIR;

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
        $retailerImage = $this->_config->getValue('dividebuy/general/retailer_image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $productBannerImage = $this->_config->getValue('dividebuy/product/banner_image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $extensionStatus = $this->_config->getValue(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_EXTENSION_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!empty($this->_backendModelSession->getPreviousRetailerImage()) && $this->_backendModelSession->getPreviousRetailerImage() != $retailerImage) {
            $fullPath = $dividebuyMediaDirectory . $this->_backendModelSession->getPreviousRetailerImage();
            if ($this->_fileManagement->isExists($fullPath)) {
                $this->_fileManagement->deleteFile($fullPath);
            }
        }
        if (!empty($this->_backendModelSession->getPreviousProductBannerImage()) && $this->_backendModelSession->getPreviousProductBannerImage() != $productBannerImage) {
            $fullPath = $dividebuyMediaDirectory . $this->_backendModelSession->getPreviousProductBannerImage();
            if ($this->_fileManagement->isExists($fullPath)) {
                $this->_fileManagement->deleteFile($fullPath);
            }
        }
        if (!empty($this->_backendModelSession->getPreviousCheckoutButtonHoverImage()) && $this->_backendModelSession->getPreviousCheckoutButtonHoverImage() != $checkoutButtonHoverImage) {
            $fullPath = $dividebuyMediaDirectory . $this->_backendModelSession->getPreviousCheckoutButtonHoverImage();
            if ($this->_fileManagement->isExists($fullPath) && !strpos($this->_backendModelSession->getPreviousCheckoutButtonHoverImage(), 'default')) {
                $this->_fileManagement->deleteFile($fullPath);
            }
        }
        if (!empty($this->_backendModelSession->getPreviousCheckoutButtonImage()) && $this->_backendModelSession->getPreviousCheckoutButtonImage() != $checkoutButtonImage) {
            $fullPath = $dividebuyMediaDirectory . $this->_backendModelSession->getPreviousCheckoutButtonImage();
            if ($this->_fileManagement->isExists($fullPath) && !strpos($this->_backendModelSession->getPreviousCheckoutButtonImage(), 'default')) {
                $this->_fileManagement->deleteFile($fullPath);
            }
        }

        $dividebuyGlobalDeactivate = $this->_scopeConfig->getValue(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_GLOBAL_DEACTIVATED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        if ($dividebuyGlobalDeactivate != 1) {
            if ($extensionStatus != $this->_backendModelSession->getPreviousExtensionStatus()) {
                $this->updateRetailerStatusInDividebuy($extensionStatus, $storeId);
            }

            // unsetting all the sessions
            $this->_backendModelSession->unsPreviousRetailerImage();
            $this->_backendModelSession->unsPreviousProductBannerImage();
            $this->_backendModelSession->unsPreviousCheckoutButtonHoverImage();
            $this->_backendModelSession->unsPreviousCheckoutButtonImage();
            $this->_backendModelSession->unsPreviousExtensionStatus();

            $this->updateInDividebuy($storeCode);
            $this->setDividebuyEnableDefaultValue($storeId);
        } else {
            $this->messageManager->addError(__('Retail Partner has been deactivated from DivideBuy. If you believe this is in error, please contact your account manager or email: retailpartners@dividebuy.co.uk'));
            $this->clearRetailerConfigurations($storeId);
        }
    }

    /**
     * updateRetailerStoreCode in dividebuy
     *
     * @param $storeCode
     */
    public function updateInDividebuy($storeCode)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeId = $this->storeManager->getStore()->getId();

        $tokenNumber = $this->_helper->getTokenNumber($storeId);
        $authNumber = $this->_helper->getAuthNumber($storeId);
        $params = array(
            'storeToken' => $tokenNumber,
            'storeAuthentication' => $authNumber,
            'retailerStoreCode' => $storeCode,
            'CallRetailer' => 1,
        );

        $params = $this->jsonHelper->jsonEncode($params);

        $url = $this->_retailerConfigurationHelper->getApiUrl($this->_helper->getStoreId()) . 'api/confirmretailer';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        try {
            $response = curl_exec($ch);
            $response = json_decode($response, true);
            /*For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= 'Url :' . $url . " \n";
                $dataResponse .= 'Param :' . $params . "\n";
                $dataResponse .= 'Response :' . $response . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*Error log code end*/
            $scope = 'stores';
            if (!$storeId) {
                $storeId = 0;
                $scope = 'default';
            }
            if ($response['status'] === 'ok') {
                $config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');

                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_RETAILER_ID, $response['retailerId'], $scope, $storeId);
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'retailer.log');
        }
    }

    /**
     * Updates the Dividebuy enable attribute default value
     */
    public function setDividebuyEnableDefaultValue($storeId)
    {
        $dividebuyEnableDefaultConfigValue = $this->_helper->getProductDividebuyEnableDefaultConfigValue($storeId);

        $attributeCode = 'dividebuy_enable';

        $dividebuyEnableAttribute = $this->_eavAttribute->loadByCode('catalog_product', $attributeCode);

        $dividebuyEnableAttribute->setDefaultValue($dividebuyEnableDefaultConfigValue)->save();
    }

    /**
     * Send an API request to Core API to update retailer status
     *
     * @param int $retailerStatus
     * @param int $storeId
     */
    public function updateRetailerStatusInDividebuy($retailerStatus, $storeId)
    {
        $tokenNumber = $this->_helper->getTokenNumber($storeId);
        $authNumber = $this->_helper->getAuthNumber($storeId);
        $allowedIps = $this->_helper->getAllowedIps($storeId);
        $params = array(
            'storeToken' => $tokenNumber,
            'storeAuthentication' => $authNumber,
            'retailerStatus' => $retailerStatus,
            'allowedIps' => $allowedIps,
        );

        $params = $this->jsonHelper->jsonEncode($params);

        $url = $this->_retailerConfigurationHelper->getApiUrl($this->_helper->getStoreId()) . 'api/updateretailerstatus';

        $this->_checkoutConfigApiHelper->sendRequest($url, $params);
    }

    public function clearRetailerConfigurations($storeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
        $scope = 'stores';

        if ($storeId == '') {
            $storeId = 0;
            $scope = 'default';
        }

        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_TOKEN_NUMBER, null, $scope, $storeId);
        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_AUTH_NUMBER, null, $scope, $storeId);
        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_RETAILER_ID, null, $scope, $storeId);
        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_EXTENSION_STATUS, 0, $scope, $storeId);
    }
}
