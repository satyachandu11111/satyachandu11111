<?php

namespace Dividebuy\Payment\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DIVIDEBUY_PLACE_ORDER_URL = '';
    const DIVIDEBUY_PAYMENT_CODE = 'dbpayment';
    const DIVIDEBUY_ORDER_STATUS = 'dividebuy_pending';
    const DIVIDEBUY_XML_PATH_POS_SHIPPING = 'carriers/dividebuyposshipping/active';

    /**
     * \Magento\Sales\Model\Order
     *
     * @var object
     */
    protected $_orderModel;

    /**
     * \Magento\Framework\App\Action\Context
     *
     * @var object
     */
    protected $_actionContext;

    /**
     * \Magento\Framework\Json\Helper\Data
     *
     * @var object
     */
    protected $_jsonHelper;

    /**
     * \Magento\Sales\Api\OrderRepositoryInterface
     *
     * @var object
     */
    protected $_orderRepository;

    /**
     * \Magento\Sales\Model\Service\InvoiceService
     *
     * @var object
     */
    protected $_invoiceService;

    /**
     * \Magento\Framework\DB\Transaction
     *
     * @var object
     */
    protected $_transaction;

    /**
     * \Magento\Store\Model\ScopeInterface
     *
     * @var object
     */
    protected $_scopeConfig;

    /**
     * \Magento\Framework\Registry
     *
     * @var object
     */
    protected $_divideBuylogger;

    /**
     * \Magento\Catalog\Api\ProductRepositoryInterface
     *
     * @var object
     */
    protected $_productRepository;

    /**
     * \Magento\Store\Model\StoreManagerInterface
     *
     * @var object
     */
    protected $_storeManager;

    /**
     * \Magento\Quote\Api\CartRepositoryInterface
     *
     * @var object
     */
    protected $_cartRepositoryInterface;

    /**
     * \Magento\Quote\Api\CartManagementInterface
     *
     * @var object
     */
    protected $_cartManagementInterface;

    /**
     * \Magento\Checkout\Model\Session
     *
     * @var object
     */
    protected $_checkoutSession;

    /**
     * \Magento\Framework\Registry
     *
     * @var object
     */
    protected $_registry;
    
    /**
     * \Magento\Catalog\Model\Product
     *
     * @var object
     */
    protected $_productModel;

    /**
     * SKU of DivideBuy POS product.
     *
     * @var string
     */
    protected $_productSku = 'dividebuy-pos-product';

    /**
     * Constructor for DivideBuy payment helper.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Action\Context $actionContext
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Sales\Model\Order $orderRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Action\Context $actionContext,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Sales\Model\Order $orderRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\Registry $registry
    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->_actionContext = $actionContext;
        $this->_orderRepository = $orderRepository;
        $this->_productRepository = $productRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_divideBuylogger = $divideBuylogger;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_cartRepositoryInterface = $cartRepositoryInterface;
        $this->_cartManagementInterface = $cartManagementInterface;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteloader = $quoteFactory;
        $this->_productModel = $productModel;
        parent::__construct($context);
    }

    /**
     * Used set order details
     *
     * @param object $order
     * @param array $userAddress
     */
    public function setOrderData($order, $userAddress)
    {
        // Saving Customer Information
        $order->setCustomerFirstname($userAddress['firstname']);
        $order->setCustomerLastname($userAddress['lastname']);
        $order->setCustomerEmail($userAddress['email']);

        try {
            $order->save();
        } catch (Exception $ex) {
            return false;
        }

        $orderShipping = $order->getShippingAddress();
        $orderShipping->setPrefix($userAddress['prefix']);
        $orderShipping->setFirstname($userAddress['firstname']);
        $orderShipping->setLastname($userAddress['lastname']);
        $orderShipping->setStreet($userAddress['street']);
        $orderShipping->setPostcode($userAddress['postcode']);
        $orderShipping->setRegion($userAddress['region']);
        $orderShipping->setCity($userAddress['city']);
        $orderShipping->setEmail($userAddress['email']);
        $orderShipping->setTelephone($userAddress['telephone']);

        $orderBilling = $order->getBillingAddress();
        $orderBilling->setPrefix($userAddress['prefix']);
        $orderBilling->setFirstname($userAddress['firstname']);
        $orderBilling->setLastname($userAddress['lastname']);
        $orderBilling->setStreet($userAddress['street']);
        $orderBilling->setPostcode($userAddress['postcode']);
        $orderBilling->setRegion($userAddress['region']);
        $orderBilling->setCity($userAddress['city']);
        $orderBilling->setEmail($userAddress['email']);
        $orderBilling->setTelephone($userAddress['telephone']);

        $orderId = $order->getId();

        try {
            $order->save();
            $orderShipping->save();
            $orderBilling->save();
            $result = array(
                'error' => 0,
                'success' => 1,
                'status' => 'ok',
                'order_id' => $order->getId(),
                'message' => 'Order placed successfully',
            );
            /*For error log code start*/
            $errorLogStatus = $this->scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Create order postalcode false \n";
                $dataResponse .= 'Error message :' . json_encode($e->getMessage()) . "\n";
                $dataResponse .= 'Response :' . json_encode($result) . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*Error Log code end*/
        } catch (Exception $e) {
            $result = array(
                'error' => 1,
                'order_id' => $order->getId(),
                'message' => 'order not found',
            );
            /*For error log code start*/
            $errorLogStatus = $this->scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Create order not found\n";
                $dataResponse .= 'Error message :' . json_encode($e->getMessage()) . "\n";
                $dataResponse .= 'Response :' . json_encode($result) . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*error log code end*/
        }
        return $result;
    }

    /**
     * Checking authentication
     *
     * @param string $token
     * @param string $authentication
     *
     * @return mixed
     */
    public function checkAuth($token, $authentication, $storeId)
    {
        $tokenNumber = $this->scopeConfig->getValue('dividebuy/general/token_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $authenticationKey = $this->scopeConfig->getValue('dividebuy/general/auth_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$token || !$authentication || $token != $tokenNumber || $authentication != $authenticationKey) {
            $response = array('error' => 1, 'success' => 0, 'message' => 'Authentication failed');
            return $response;
        } else {
            return false;
        }
    }

    /**
     * Prepare JSON formatted data for response to client
     *
     * @param $response
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function _prepareDataJSON($response)
    {
        $this->_actionContext->getResponse()
            ->setHeader('Content-type', 'application/json', true);
        return $this->_actionContext->getResponse()
            ->setBody(json_encode($response));
    }

    /**
     * Generate invoice of given order id
     *
     * @param $orderId
     *
     * @return mixed
     */
    public function generateInvoice($orderId)
    {
        $order = $this->_orderRepository->load($orderId);
        if ($order->canInvoice()) {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->_transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
        }
    }

    /**
     * Used to delete order by orderID
     *
     * @param int $orderId
     *
     * @return void
     */
    public function deleteOrder($orderToBeDelete, $post, $storeId)
    {
        if (is_array($post)) {
            $post = $this->_jsonHelper->jsonEncode($post);
        }
        $this->_registry->register('isSecureArea', true);
        $orderToBeDelete->delete();
        $this->_registry->unregister('isSecureArea');

        $result = array('error' => 0, 'success' => 1, 'status' => 'ok');
        /* For error log code start*/
        $errorLogStatus = $this->scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, empty($storeId) ? 0 : $storeId);
        if ($errorLogStatus == 1) {
            $dataResponse = "============\n";
            $dataResponse .= "Url :DivideBuy order Deleted\n";
            $dataResponse .= 'Param :' . $post . "\n";
            $dataResponse .= 'Response :' . json_encode($result) . "\n";
            $this->_divideBuylogger->info($dataResponse);
        }
        /*Error log code end*/
        return $this->_prepareDataJSON($result);
    }

    public function completeOrder($orderId, $orderDetails)
    {
        $order = $this->_orderRepository->load($orderId);

        $orderTime = $orderDetails['orderTime'];
        $orderReferenceId = $orderDetails['laravel_order_ref_id'];
        $address = $orderDetails['address'];
        $street1 = $address['house_number'] . ' ' . $address['house_name'] . ', ' . $address['street'];
        $street1 = ltrim($street1, ', ');
        $street2 = $address['address2'];
        $street = array(
            '0' => $street1,
            '1' => $street2,
        );

        $isPhoneOrderEnabled = $orderDetails['is_phone_order_enabled'];
        $customerEmail = $orderDetails['customer_email'];
        $userAddress = array(
            'prefix' => $address['prefix'],
            'firstname' => $address['first_name'],
            'lastname' => $address['last_name'],
            'street' => $street,
            'postcode' => $address['postcode'],
            'region' => $address['region'],
            'city' => $address['city'],
            'email' => $customerEmail,
            'telephone' => $address['contact_number'],
        );
        $order->setHideDividebuy(0);
        $order->setCreatedAt($orderTime);
        // Checking if order is placed via phone order.
        if ($isPhoneOrderEnabled == 1) {
            $order->addStatusHistoryComment('DivideBuy order authenticated via phone order. Transaction ID : "' . $orderReferenceId . '"', \Magento\Sales\Model\Order::STATE_PROCESSING);
        } else {
            $order->addStatusHistoryComment('DivideBuy order authenticated. Transaction ID : "' . $orderReferenceId . '"', \Magento\Sales\Model\Order::STATE_PROCESSING);
        }
        $order->save();

        // Sending retailer email
        $orderEmail = $this->_orderRepository->load($orderId);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->create('\Magento\Sales\Model\OrderNotifier')->notify($orderEmail);

        // Setting Order with address
        $completeOrder = $this->_orderRepository->load($orderId);
        $this->setOrderData($completeOrder, $userAddress);

        return true;
    }

    /**
     * This function creates new product for POS.
     *
     * @param int $storeId
     *
     * @return int
     */
    public function createPosProduct($websiteId)
    {
        // Checking if product is already exists
        $customPosProductId = $this->getPosProductId();

        if ($customPosProductId == '') {
            $this->_productModel->setSku('dividebuy-pos-product');
            $this->_productModel->setName('Dividebuy Custom Order Product');
            $this->_productModel->setWebsiteId($websiteId);
            $this->_productModel->setAttributeSetId(4);
            $this->_productModel->setStatus(1);
            $this->_productModel->setVisibility(1);
            $this->_productModel->setTaxClassId(0);
            $this->_productModel->setTypeId('simple');
            $this->_productModel->setPrice(1);
            $this->_productModel->setStockData(
                array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 0,
                    'is_in_stock' => 1,
                )
            );
            $this->_productModel->save();
            return $this->_productModel->getId();
        }

        // Enabling custom order product.
        $customProduct = $this->_productModel->load($customPosProductId);
        $customProduct->setStatus(1);
        $customProduct->save();

        return $customPosProductId;
    }

    /**
     * Function used for activate/deactivate POS shipping.
     *
     * @param int $activateFlag
     * @param int $storeId
     *
     * @return bool
     */
    public function activateDeactivatePosShipping($activateFlag, $storeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
        $scope = 'stores';

        if ($storeId == '') {
            $storeId = 0;
            $scope = 'default';
        }

        // Updating shipping configuration.
        $config->saveConfig(self::DIVIDEBUY_XML_PATH_POS_SHIPPING, $activateFlag, $scope, $storeId);

        return true;
    }

    /**
     * This function is used for creating DivideBuy POS order.
     *
     * @param array $postData
     *
     * @return array
     */
    public function createPosOrder($postData)
    {
        $store = $this->_storeManager->getStore();
        $storeId = $this->_storeManager->getStore()->getStoreId();

        // Creating empty cart then assigning quote to store.
        $cartId = $this->_cartManagementInterface->createEmptyCart();
        $quote = $this->_cartRepositoryInterface->get($cartId);

        // Storing quote ID into session.
        $this->_checkoutSession->setDividebuyCustomQuoteId($quote->getId());
        $quote->setStore($store);

        // Adding product into quote after updating product price.
        $posProductId = $this->getPosProductId();
        $product = $this->_productModel->setStoreId($storeId)->load($posProductId);

        // Updating product price
        $product->setPrice($postData['order_total']);
        $product->save();
        $quote->addProduct($product, 1);

        // Assiging address to quote.

        $updatedAddress = $postData['address'];
        $addressStreet = $updatedAddress['house_name']. ', ' . $updatedAddress['street'] . ', ' . $updatedAddress['address2'];
        $orderAddress = array(
            'firstname' => $updatedAddress['first_name'],
            'lastname' => $updatedAddress['last_name'],
            'email' => $postData['customer_email'],
            'street' => ltrim($addressStreet, ','),
            'city' => $updatedAddress['city'],
            'country_id' => 'GB',
            'region' => $updatedAddress['region'],
            'postcode' => $updatedAddress['postcode'],
            'telephone' => $updatedAddress['contact_number'],
        );
        $quote->getBillingAddress()->addData($orderAddress);
        $shippingAddress = $quote->getShippingAddress()->addData($orderAddress);

        // Adding shipping method to quote.
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('dividebuyposshipping_dividebuyposshipping');
        $quote->setPaymentMethod('dbpayment');

        // Assigning user as guest for order.
        $quote->setCheckoutMethod('guest')
            ->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true);

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'dbpayment']);

        // Removing discounts from quote
        $quote->setCouponCode('');
        $quote->setAppliedRuleId('');
        $quote->save();

        // Collect Totals
        $quote->collectTotals();

        // Creating order from quote.
        $quote = $this->_cartRepositoryInterface->get($quote->getId());
        $orderId = $this->_cartManagementInterface->placeOrder($quote->getId());
        $order = $this->_orderRepository->load($orderId);

        $order->setEmailSent(0);

        // Updating order information.
        $order->setData('hide_dividebuy', 0);
        $order->setCustomerFirstname($updatedAddress['first_name']);
        $order->setCustomerLastname($updatedAddress['last_name']);
        $order->setCustomerEmail($postData['customer_email']);
        $order->setCreatedAt($postData['orderTime']);
        $order->addStatusHistoryComment('Custom Phone Order - DivideBuy: </br>' . $postData['comment_detail'], \Magento\Sales\Model\Order::STATE_PROCESSING);

        $order->save();
        
        // Generating order invoice.
        if ($order->canInvoice()) {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->_transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
        }

        // Clearing quote session.
        $this->_checkoutSession->unsDividebuyCustomQuoteId();
        return $order;
    }

    /**
     * Function for getting custom POS product id
     *
     * @return int
     */
    public function getPosProductId()
    {
        try {
            $product = $this->_productRepository->get($this->_productSku);
            if (!empty($product->getId())) {
                return $product->getId();
            }
        } catch (\Exception $e) {
            return false;
        }
        
    }

    /**
     * This function is used for checking if shipping method is available for provided quote or not.
     *
     * @param int $quoteId
     *
     * @return bool
     */
    public function isShippingMethodAvailable($quoteId)
    {
        $quote = $this->_quoteloader->create()->load($quoteId);
        foreach ($quote->getAllItems() as $item) {
            $productSKU = $item->getProduct()->getSKU();
            if ($productSKU == 'dividebuy-pos-product') {
                return true;
            }
        }
        return false;
    }
}
