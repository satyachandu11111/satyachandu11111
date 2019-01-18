<?php

namespace Dividebuy\Payment\Controller\Api;

use Magento\Framework\App\Action\Context;

class ActivatePosSystem extends \Magento\Framework\App\Action\Action
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
     * Constructor for activate POS API.
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
        $websiteId = $store->getWebsiteId();
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
                $dataResponse .= "Url : dividebuy/api/activatepossystem\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            return;
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
                $dataResponse = "========= Store authentication falied ========\n";
                $dataResponse .= "Url : dividebuy/api/activatepossystem\n";
                $dataResponse .= "Param :".$post."\n";
                $dataResponse .= "Response :".json_encode($result)."\n";
                $this->_divideBuylogger->info($dataResponse);    
            }
            
            $this->_paymentHelper->_prepareDataJSON($result);
            return;
        } else {
            if ($postData['activate_pos'] == NULL) {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'An error occurred. We are unable to activate POS on Retailer\'s end',
                    'status' => '422',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }
            
            // Checking if POS is activated.
            if ($postData['activate_pos'] == 1) {
                // Creating POS product and activating shipping.
                $posProduct = $this->_paymentHelper->createPosProduct($websiteId);
                $this->_paymentHelper->activateDeactivatePosShipping(1, $storeId);
                if ($posProduct) {
                    $result = array(
                        'error' => 0,
                        'success' => 1,
                        'status' => 'OK',
                    );
                } else {
                    $result = array(
                        'error' => 1,
                        'success' => 0,
                        'message' => 'An error occurred. We are unable to create the product on Retailer\'s end',
                        'status' => '422',
                    );
                    // Generating error log.
                    if ($errorLogStatus == 1) {
                        $dataResponse = "======= Activate POS failed. ======\n";
                        $dataResponse .= "Url : dividebuy/api/activatepossystem\n";
                        $dataResponse .= "Param :".$post."\n";
                        $dataResponse .= "Response :".json_encode($result)."\n";
                        $this->_divideBuylogger->info($dataResponse);    
                    }
                }
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            } elseif ($postData['activate_pos'] == 0) {
                // Disabling product and custom shipping when POS is deactivated.
                $product = $this->_productRepository->get($this->_productSku);
                $product->setStatus(2);
                $product->save();
                $this->_paymentHelper->activateDeactivatePosShipping(0, $storeId);
                $result = array(
                    'error' => 0,
                    'success' => 1,
                    'status' => 'OK',
                );
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            } else {
                $result = array(
                    'error' => 1,
                    'success' => 0,
                    'message' => 'An error occurred. We are unable to activate POS on Retailer\'s end',
                    'status' => '422',
                );
                if ($errorLogStatus == 1) {
                    $dataResponse = "======= Activate POS failed. ======\n";
                    $dataResponse .= "Url : dividebuy/api/activatepossystem\n";
                    $dataResponse .= "Param :".$post."\n";
                    $dataResponse .= "Response :".json_encode($result)."\n";
                    $this->_divideBuylogger->info($dataResponse);    
                }
                $this->_paymentHelper->_prepareDataJSON($result);
                return;
            }
        }
    }
}
