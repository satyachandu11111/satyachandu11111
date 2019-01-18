<?php

namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class CreateCustomPosOrder extends \Magento\Framework\App\Action\Action
{
    /**
     * \Magento\Framework\Json\Helper\Data
     *
     * @var object
     */
    protected $_jsonHelper;

    /**
     * \Dividebuy\Payment\Helper\Data
     *
     * @var object
     */
    protected $_paymentHelper;

    /**
     * \Magento\Store\Model\Store
     *
     * @var object
     */
    protected $_storeModel;

    /**
     * \Magento\Catalog\Api\ProductRepositoryInterface
     *
     * @var object
     */
    protected $_productRepository;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;
    
    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * SKU of DivideBuy POS product.
     *
     * @var string
     */
    protected $_productSku = 'dividebuy-pos-product';

    /**
     * Constructor for POS order API.
     *
     * @param Context $context
     * @param \Dividebuy\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Store\Model\Store $storeModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Model\Quote $quoteModel
     * @param \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\Quote $quoteModel,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_jsonHelper = $jsonHelper;
        $this->_quoteModel = $quoteModel;
        $this->_productRepository = $productRepository;
        $this->_storeModel = $storeModel;
        $this->_divideBuylogger = $divideBuylogger;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * This function will be executed once Activate POS API will be called from DivideBuy.
     *
     * @return JSON
     */
    public function execute()
    {
        $post = trim(file_get_contents('php://input'));
        $postData = $this->_jsonHelper->jsonDecode($post);
        $storeCode = $postData['retailer_store_code'];
        $store = $this->_storeModel->load($storeCode, 'code');
        $storeId = $store->getStoreId();
        $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
        
        if (!$postData) {
            $result = array(
                'error' => 1,
                'success' => 0,
                'message' => 'An error occurred. We are unable to retrieve the data via the API',
                'status' => '422',
            );
            $this->_paymentHelper->_prepareDataJSON($result);
            
            // Generating error log.
            if ($errorLogStatus == 1) {
                $dataResponse = "======= Unable to fetch post data. ======\n";
                $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            return;
        }

        // Validation of postData values.
        if (!$postData['customer_email'] || !filter_var($postData['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $result = array(
                'error' => 1,
                'success' => 0,
                'message' => 'Customer email is blank or invalid',
                'status' => '422',
            );
            // Generating error log.
            if ($errorLogStatus == 1) {
                $dataResponse = "======= Invalid customer email. ======\n";
                $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        // Checking of order time is invalid.
        if (!$postData['orderTime']) {
            $result = array(
                'error' => 1,
                'success' => 0,
                'message' => 'Order time is blank or invalid',
                'status' => '422',
            );
            // Generating error log.
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if ($errorLogStatus == 1) {
                $dataResponse = "======= Order time is null. ======\n";
                $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        }

        // Checking for blank address fields.
        foreach ($postData['address'] as $addressKey => $addressValue) {
            if ($addressKey != 'house_name' && $addressKey != 'house_number' && $addressKey != 'address2' && !$addressValue) {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => $addressKey . ' is a required field.',
                    'status' => '422',
                );
                // Generating error log.
                if ($errorLogStatus == 1) {
                    $dataResponse = "======= Address fields is missing. ======\n";
                    $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                    $dataResponse .= "Param :".$post."\n";
                    $dataResponse .= "Response :".json_encode($result)."\n";
                    $this->_divideBuylogger->info($dataResponse);    
                }
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }
        }

        // Checking for store authentication.
        $storeToken = $postData['store_token'];
        $storeAuthentication = $postData['store_authentication'];

        $authenticationStatus = $this->_paymentHelper->checkAuth($storeToken, $storeAuthentication, $storeId);
        if ($authenticationStatus) {
            $result = array(
                'error' => 1,
                'success' => 0,
                'message' => 'Store authentication has been failed.',
                'status' => '422',
            );
            // Generating error log.
            if ($errorLogStatus == 1) {
                $dataResponse = "======= Store authentication has been failed. ======\n";
                $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        } else {
            // Loading DivideBuy POS product.
            $product = $this->_productRepository->get($this->_productSku);
            if ($product->getStatus() == 2) {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'DivideBuy POS prouduct is disabled.',
                    'status' => '422',
                );
                // Generating error log.
                if ($errorLogStatus == 1) {
                    $dataResponse = "======= DivideBuy product is disabled. ======\n";
                    $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                    $dataResponse .= "Param :".$post."\n";
                    $dataResponse .= "Response :".json_encode($result)."\n";
                    $this->_divideBuylogger->info($dataResponse);    
                }
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            } elseif (!is_numeric($postData['order_total']) && !is_float(($postData['order_total']))) {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'Order total is invalid.',
                    'status' => '422',
                );
                // Generating error log.
                if ($errorLogStatus == 1) {
                    $dataResponse = "======= Order total is invalid. ======\n";
                    $dataResponse .= "Url : dividebuy/api/createcustomposorder\n";
                    $dataResponse .= "Param :".$post."\n";
                    $dataResponse .= "Response :".json_encode($result)."\n";
                    $this->_divideBuylogger->info($dataResponse);    
                }
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            } else {
                // Placing POS order to show in retailer sales grid.
                $createPosOrder = $this->_paymentHelper->createPosOrder($postData);
                $result = array(
                    'error' => 0,
                    'success' => 1,
                    'order_id' => $createPosOrder->getID(),
                    'order_increment_id' => $createPosOrder->getIncrementID(),
                    'status' => 'OK',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
            }
        }
    }
}
