<?php

namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class Response extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutConfigHelper;

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_storeModel;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;

    /**
     * @param Context $context
     * @param \Dividebuy\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Store\Model\Store $storeModel
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Store\Model\Store $storeModel,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_paymentHelper = $paymentHelper;
        $this->_checkoutConfigHelper = $checkoutConfigHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_orderModel = $orderModel;
        $this->_resourceConfig = $resourceConfig;
        $this->_storeModel = $storeModel;
        $this->_divideBuylogger = $divideBuylogger;
        parent::__construct($context);
    }

    /**
     * Used to redirect to another action according to the request
     *
     * @return mixed
     */
    public function execute()
    {
        $post = trim(file_get_contents('php://input'));
        $postData = $this->_jsonHelper->jsonDecode($post);
        $storeToken = $postData['store_token'];
        $storeAuthentication = $postData['store_authentication'];
        $storeCode = $postData['retailer_store_code'];
        $store = $this->_storeModel->load($storeCode, 'code');
        $storeId = $store->getStoreId();

        // Checking if allowed IPs are coming in postData or not.
        if (array_key_exists('allowedIps', $postData)) {
            $dividebuyEnvironment = $this->_scopeConfig->getValue('dividebuy/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
            if ($dividebuyEnvironment == 'staging' && !$postData['allowedIps']) {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'Update allowed IPs failed as retailer is pointed to sandbox environment.',
                    'status' => '422',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            } else {
                // Updating allowed IPs field based on postData.
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
                $scope = 'stores';

                if ($storeId == '') {
                    $storeId = 0;
                    $scope = 'default';
                }
                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_ALLOWED_IP, $postData['allowedIps'], $scope, $storeId);
            }
        }

        // Code Activate the extension if Deactivated by Admin Panel
        if (isset($postData['is_deactivated'])) {
            if ($postData['is_deactivated'] == 0) {
                if (($this->_scopeConfig->getValue(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_GLOBAL_DEACTIVATED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId) == 1)) {
                    $result = $this->activateDividebuyExtension($storeId, $postData);
                    $this->_paymentHelper->_prepareDataJSON($result);
                    return;
                }
                $result = array(
                    'error' => 0,
                    'success' => 1,
                    'message' => 'OK',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }
        }

        $authenticationStatus = $this->_paymentHelper->checkAuth($storeToken, $storeAuthentication, $storeId);
        if (!empty($postData)) {
            if ($authenticationStatus) {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'Store Authentication Failed',
                    'status' => '401',
                );
                /* For error log code start*/
                $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
                if ($errorLogStatus == 1) {
                    $dataResponse = "============\n";
                    $dataResponse .= "Url :authentication failed in response api\n";
                    $dataResponse .= 'Param :' . $post . "\n";
                    $dataResponse .= 'Response :' . json_encode($result) . "\n";
                    $this->_divideBuylogger->info($dataResponse);
                }
                /* For error log code end*/

                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            } elseif (!empty($postData['method']) && ($postData['method'] == 'orderSuccess' || $postData['method'] == 'orderCancel' || $postData['method'] == 'orderDelete')) {
                $order = $this->_orderModel->load($postData['store_order_id']);
                if ($order->getId()) {
                    $paymentMethod = $order->getPayment()->getMethod();
                    if ($paymentMethod == \Dividebuy\Payment\Helper\Data::DIVIDEBUY_PAYMENT_CODE) {
                        if ($postData['method'] == 'orderSuccess') {
                            $this->_forward('successOrder');
                        }
                        if ($postData['method'] == 'orderCancel') {
                            $this->_forward('cancelOrder');
                        }
                        if ($postData['method'] == 'orderDelete') {
                            $flagToDeleteOrders = $this->_scopeConfig->getValue('dividebuy/global/flag_to_delete_orders', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            $order->setHideDividebuy(0);
                            $order->cancel()
                                ->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Order cancelled from dividebuy')
                                ->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Order cancelled from dividebuy');
                            $order->save();
                            if ($flagToDeleteOrders) {
                                $this->_paymentHelper->deleteOrder($order, $post, $storeId);
                                return;
                            } else {
                                $result = array(
                                    'error' => 0,
                                    'success' => 1,
                                    'status' => 'ok',
                                );
                                /* For error log code start*/
                                $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
                                if ($errorLogStatus == 1) {
                                    $dataResponse = "============\n";
                                    $dataResponse .= "Url :DivideBuy order Deleted\n";
                                    $dataResponse .= 'Param :' . $post . "\n";
                                    $dataResponse .= 'Response :' . json_encode($result) . "\n";
                                    $this->_divideBuylogger->info($dataResponse);
                                }
                                /* error log code end*/
                                $this->_paymentHelper->_prepareDataJSON($result);
                                return;
                            }
                        }
                    } else {
                        $result = array(
                            'error' => 1,
                            'success' => 0,
                            'message' => 'Non-DivideBuy order',
                            'status' => '402',
                        );
                        /* For error log code start*/
                        $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
                        if ($errorLogStatus == 1) {
                            $dataResponse = "============\n";
                            $dataResponse .= "Url :Non-DivideBuy order\n";
                            $dataResponse .= 'Param :' . $post . "\n";
                            $dataResponse .= 'Response :' . json_encode($result) . "\n";
                            $this->_divideBuylogger->info($dataResponse);
                        }
                        /* error log code end*/
                        $this->_paymentHelper->_prepareDataJSON($result);
                        return;
                    }
                } else {
                    $result = array(
                        'error' => 1,
                        'success' => 0,
                        'message' => 'order not found',
                        'status' => '403',
                    );
                    /* For error log code start*/
                    $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
                    if ($errorLogStatus == 1) {
                        $dataResponse = "============\n";
                        $dataResponse .= "Url :order not found\n";
                        $dataResponse .= 'Param :' . $post . "\n";
                        $dataResponse .= 'Response :' . json_encode($result) . "\n";
                        $this->_divideBuylogger->info($dataResponse);
                    }
                    /* error log code end*/
                    $this->_paymentHelper->_prepareDataJSON($result);
                    return;
                }
            } elseif (!empty($postData['method']) && $postData['method'] == 'retailerConfigurations') {
                $this->addRetailerConfigurations($postData, $post, $storeId); //adding non deliverable postcodes
            } elseif (!empty($postData['method']) && $postData['method'] == 'updateCouriers') {
                $this->updateCouriers($postData, $post, $storeId); //updating couriers
            } else {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'Method not found',
                    'status' => '404',
                );
                /* For error log code start*/
                $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
                if ($errorLogStatus == 1) {
                    $dataResponse = "============\n";
                    $dataResponse .= "Url :Response api Method not found \n";
                    $dataResponse .= 'Param :' . $post . "\n";
                    $dataResponse .= 'Response :' . json_encode($result) . "\n";
                    $this->_divideBuylogger->info($dataResponse);
                }
                /* Error log code end*/
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }
        } else {
            $result = array(
                'error' => 1,
                'success' => 0,
                'message' => 'There is a problem in retriving data',
                'status' => '406',
            );
            /* For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= "Url :Api response issue There is a problem in retriving data \n";
                $dataResponse .= 'Param :' . $post . "\n";
                $dataResponse .= 'Response :' . json_encode($result) . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*Error log code end*/
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }
    }

    /**
     * Adds the nondeliverable post code
     *
     * @param array $data
     *
     * @return mixed
     */
    public function addRetailerConfigurations($data, $post, $storeId)
    {
        if (isset($data['is_deactivated'])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
            $scope = 'stores';

            if ($storeId == '') {
                $storeId = 0;
                $scope = 'default';
            }
            if ($data['is_deactivated'] == 1) {
                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_TOKEN_NUMBER, null, $scope, $storeId);
                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_AUTH_NUMBER, null, $scope, $storeId);
                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_RETAILER_ID, null, $scope, $storeId);
                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_EXTENSION_STATUS, 0, $scope, $storeId);
                $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_GLOBAL_DEACTIVATED, 1, $scope, $storeId);
                $result = array(
                    'error' => 0,
                    'success' => 1,
                    'status' => 'ok',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }
        }

        $configurationDetails = $data['retailerConfigurationDetails'];
        $instalments = array();
        $j = 0;
        $value = '';
        $field = '';
        $flag = false;

        for ($i = 0; $i < sizeof($configurationDetails); $i++) {
            if ($configurationDetails[$i]['type'] == 'global' && $configurationDetails[$i]['key'] == 'excludePostCodes') {
                $value = $configurationDetails[$i]['value'];
                $field = 'exclude_post_codes';
            } elseif ($configurationDetails[$i]['type'] == 'global' && $configurationDetails[$i]['key'] == 'taxClass') {
                $value = $configurationDetails[$i]['value'];
                $field = 'tax_class';
            } elseif ($configurationDetails[$i]['type'] == 'global' && $configurationDetails[$i]['key'] == 'flagToDeleteOrders') {
                $value = (string) $configurationDetails[$i]['value'];
                $field = 'flag_to_delete_orders';
            } elseif ($configurationDetails[$i]['type'] == 'themes' && $configurationDetails[$i]['key'] == 'logoUrl') {
                $value = $configurationDetails[$i]['value'];
                $field = 'logoUrl';
            } elseif ($configurationDetails[$i]['type'] == 'instalments') {
                $instalments[$j]['key'] = $configurationDetails[$i]['key'];
                $instalments[$j]['value'] = $configurationDetails[$i]['value'];
                $j++;
            } elseif ($configurationDetails[$i]['type'] == 'global' && $configurationDetails[$i]['key'] == 'inStoreCollection') {
                $value = (string) $configurationDetails[$i]['value'];
                $field = 'retailor_auto_checkout';
            }

            if ($value != '' && $value !== 0) {
                $this->updateCoreConfigData($data, $value, $field);
                $flag = true;
            }
        }
        if (count($instalments) > 0) {
            $field = 'instalment_details';
            $allValues = array();
            for ($k = 0; $k < sizeof($instalments); $k++) {
                $allValues[] = $instalments[$k]['value'];
            }
            if (!empty(min($allValues))) {
                $minField = 'min_order';
                $minValue = min($allValues);
                $this->updateCoreConfigData($data, $minValue, $minField);
            }

            $this->updateCoreConfigData($data, serialize($instalments), $field);
            $flag = true;
        }

        if ($flag) {
            $result = array(
                'error' => 0,
                'success' => 1,
                'status' => 'ok',
            );
        } else {
            $result = array(
                'error' => 1,
                'success' => 0,
                'response_status' => '404',
            );
        }
        /* For error log code start*/
        $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
        if ($errorLogStatus == 1) {
            $dataResponse = "============\n";
            $dataResponse .= "Url :Retailer configuration api \n";
            $dataResponse .= 'Param :' . $post . "\n";
            $dataResponse .= 'Response :' . json_encode($result) . "\n";
            $this->_divideBuylogger->info($dataResponse);
        }
        /* Error log code end*/
        $this->_paymentHelper->_prepareDataJSON($result);
        return;
    }

    /**
     * Saves the data to core config data table
     *
     * @param array $data
     * @param integer/string $value that is to be saved
     * @param string $field name of field
     */
    public function updateCoreConfigData($data, $value, $field)
    {
        $scope = 'stores';
        if (empty($storeId)) {
            $storeId = 0;
            $scope = 'default';
        }
        $this->_resourceConfig->saveConfig('dividebuy/global/' . $field, $value, $scope, $storeId);
    }

    /**
     * Save courier list for DivideBuy Shipping
     *
     * @param array $data
     *
     * @return void
     */
    public function updateCouriers($data, $post, $newStoreID)
    {
        $value = serialize($data['couriers']);
        $storeId = 0;
        $scope = 'default';
        $this->_resourceConfig->saveConfig('dividebuy/global/couriers', $value, $scope, $storeId);
        $result = array('error' => 0, 'success' => 1, 'status' => 'ok');
        /* For error log code start*/
        $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $newStoreID);
        if ($errorLogStatus == 1) {
            $dataResponse = "============\n";
            $dataResponse .= "Url :Update couriers api \n";
            $dataResponse .= 'Param :' . $post . "\n";
            $dataResponse .= 'Response :' . json_encode($result) . "\n";
            $this->_divideBuylogger->info($dataResponse);
        }
        /* For error log code end*/
        $this->_paymentHelper->_prepareDataJSON($result);
        return;
    }

    public function activateDividebuyExtension($storeId, $data)
    {
        if (empty($data['store_token']) || empty($data['store_authentication'])) {
            $result = array(
                'error' => 1,
                'success' => 0,
                'status' => 'Store Token or Authentication not found.',
            );
            return $result;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
        $scope = 'stores';

        if ($storeId == '') {
            $storeId = 0;
            $scope = 'default';
        }

        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_TOKEN_NUMBER, $data['store_token'], $scope, $storeId);
        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_AUTH_NUMBER, $data['store_authentication'], $scope, $storeId);
        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_RETAILER_ID, $data['retailer_id'], $scope, $storeId);
        $config->saveConfig(\Dividebuy\RetailerConfig\Helper\Data::XML_PATH_DIVIDEBUY_GLOBAL_DEACTIVATED, 0, $scope, $storeId);

        $result = array(
            'error' => 0,
            'success' => 1,
            'message' => 'Retailer is Activated.',
        );
        return $result;
    }
}
