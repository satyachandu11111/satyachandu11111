<?php

namespace Dividebuy\Payment\Controller\Order;

use Dividebuy\CheckoutConfig\Block\Cart;
use Dividebuy\CheckoutConfig\Helper\Data as CheckoutConfigHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;

class Create extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $_onepage;

    /**
     * @var \Dividebuy\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagement;

    /**
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutConfigBlock;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CheckoutConfigHelper
     */
    protected $_checkoutConfigHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cartModel;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $_quoteItem;

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;
    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Onepage                                    $onepage
     * @param \Dividebuy\Payment\Helper\Data             $paymentHelper
     * @param \Magento\Quote\Model\QuoteManagement       $quoteManagement
     * @param Cart                                       $checkoutConfigBlock
     * @param CheckoutSession                            $checkoutSession
     * @param CustomerSession                            $customerSession
     * @param CheckoutConfigHelper                       $checkoutConfigHelper
     * @param \Magento\Checkout\Model\Cart               $cartModel
     * @param \Magento\Quote\Model\Quote\Item            $quoteItem
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Onepage $onepage,
        \Dividebuy\Payment\Helper\Data $paymentHelper,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        Cart $checkoutConfigBlock,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        CheckoutConfigHelper $checkoutConfigHelper,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_onepage              = $onepage;
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_paymentHelper        = $paymentHelper;
        $this->_checkoutConfigBlock  = $checkoutConfigBlock;
        $this->_checkoutSession      = $checkoutSession;
        $this->_customerSession      = $customerSession;
        $this->_checkoutConfigHelper = $checkoutConfigHelper;
        $this->_cartModel            = $cartModel;
        $this->_quoteItem            = $quoteItem;
        $this->_divideBuylogger      = $divideBuylogger;
        $this->_scopeConfig          = $scopeConfig;
        $this->_storeManager         = $storeManager;
        parent::__construct($context);
    }

    /**
     * Used to create an order
     *
     * @return mixed
     */
    public function execute()
    {

        //checking for non dividebuy product
        $checkCart = $this->_checkoutConfigBlock->getItemArray();
        if (isset($checkCart['nodividebuy']) && $checkCart['nodividebuy'] != 0) {
            $this->messageManager->addError(__("You still have Non-DivideBuy products in cart"));
            $response = array('error' => 1, 'success' => 0, 'message' => '', 'redirecturl' => $this->_checkoutConfigBlock->getUrl('checkout/cart', ['_secure' => true]));
            /*For error log start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Create order Order not found \n";
                $dataResponse .= "Response :" . json_encode($response) . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*error log end*/
            $this->_paymentHelper->_prepareDataJSON($response);
            return;
        }

        $zipcode            = $this->getRequest()->getParam('postcode');
        $shippingMethodCode = $this->getRequest()->getParam('shipping_method');
        $userEmail          = $this->getRequest()->getParam('user_email');

        if (!$this->_checkoutConfigHelper->getDividebuyPostcodes($zipcode)) {
            // $this->messageManager->addError(__(CheckoutConfigHelper::NON_DELIVERABLE_POSTCODE_MSG));
            $response = array('error' => 1, 'postcode' => false, 'success' => 0, 'message' => '', 'redirecturl' => $this->_checkoutConfigBlock->getUrl('checkoutconfig/index/continuetocheckout', ['_secure' => true]));
            /*For error log start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Create order postalcode false \n";
                $dataResponse .= "Response :" . json_encode($response) . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*error log end*/
            $this->_paymentHelper->_prepareDataJSON($response);
            return;
        }

        $quote = $this->_onepage->getQuote();

        if ($this->_customerSession->isLoggedIn()) {

            // Load the customer's data
            $customer  = $this->_customerSession->getCustomer();
            $firstName = $customer->getFirstname();
            $lastName  = $customer->getLastname();
            $email     = $customer->getEmail();
        } elseif ($quote->getCustomerFirstname() && $quote->getCustomerLastname() && $quote->getCustomerEmail()) {

            //check if there is any data in quote for guest user
            $firstName = $quote->getCustomerFirstname();
            $lastName  = $quote->getCustomerLastname();
            $email     = $quote->getCustomerEmail();
        } else {
            $firstName = 'First Name';
            $lastName  = 'Last Name';
            $email     = 'example@example.com';
        }

        $userBillingAddress = $quote->getBillingAddress()->getData();

        // for billing adrees street
        if (!empty($userBillingAddress['street'])) {
            $billingStreet = $userBillingAddress['street'];
        } else {
            $billingStreet = array(
                '0' => 'Brindley Court',
                '1' => 'Lymedale Business Park',
            );
        }

        // for billing address city
        if (!empty($userBillingAddress['city'])) {
            $billingCity = $userBillingAddress['city'];
        } else {
            $billingCity = 'Staffordshire';
        }

        //for billing address region
        if (!empty($userBillingAddress['region'])) {
            $billingRegion = $userBillingAddress['region'];
        } else {
            $billingRegion = 'Staffordshire';
        }

        //for billing address region
        if (!empty($userBillingAddress['telephone'])) {
            $billingTelephone = $userBillingAddress['telephone'];
        } else {
            $billingTelephone = '08000850885';
        }

        // Set Sales Order Billing Address
        $billingAddress = $quote->getBillingAddress()->addData(array(
            'firstname'  => $firstName,
            'lastname'   => $lastName,
            'email'      => $email,
            'street'     => $billingStreet,
            'city'       => $billingCity,
            'country_id' => 'GB',
            'region'     => $billingRegion,
            'postcode'   => $zipcode,
            'telephone'  => $billingTelephone,
        ));

        $userShippingAddress = $quote->getShippingAddress()->getData();

        // for Shipping Address street
        if (!empty($userShippingAddress['street'])) {
            $shippingStreet = $userShippingAddress['street'];
        } else {
            $shippingStreet = array(
                '0' => 'Brindley Court',
                '1' => 'Lymedale Business Park',
            );
        }

        // for Shipping Address city
        if (!empty($userShippingAddress['city'])) {
            $shippingCity = $userShippingAddress['city'];
        } else {
            $shippingCity = 'Staffordshire';
        }

        //for Shipping Address region
        if (!empty($userShippingAddress['region'])) {
            $shippingRegion = $userShippingAddress['region'];
        } else {
            $shippingRegion = 'Staffordshire';
        }

        //for Shipping Address address region
        if (!empty($userShippingAddress['telephone'])) {
            $shippingTelephone = $userShippingAddress['telephone'];
        } else {
            $shippingTelephone = '08000850885';
        }

        // Set Sales Order Shipping Address
        $shippingAddress = $quote->getShippingAddress()->addData(array(
            'firstname'  => $firstName,
            'lastname'   => $lastName,
            'email'      => $email,
            'street'     => $shippingStreet,
            'city'       => $shippingCity,
            'country_id' => 'GB',
            'region'     => $shippingRegion,
            'postcode'   => $zipcode,
            'telephone'  => $shippingTelephone,
        ));

        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress->setCollectShippingRates(false)
            ->collectShippingRates()
            ->setShippingMethod($shippingMethodCode); //shipping method
        $quote->setPaymentMethod('dbpayment'); //payment method

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'dbpayment']);

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        if(trim($userEmail) != ""){
            // Storing value of user email into session.
            $this->_checkoutSession->setDividebuyUserEmail($userEmail);
        }

        try {
            $result      = $this->_onepage->saveOrder();
            $redirectUrl = $this->_onepage->getCheckout()->getRedirectUrl();
            $result      = array('error' => 0, 'success' => 1, 'message' => '', 'redirecturl' => $redirectUrl);
            
            // Checking if user email value is not null.
            if(trim($userEmail) != ""){
                $phoneOrderTokenSession = $this->_checkoutSession->getDividebuyPhoneOrderToken();
                if($phoneOrderTokenSession){
                    // Getting store ID.
                    $store     = $this->_storeManager->load("default", "code");
                    $storeId   = $store->getId();
                    $portalUrl = $this->_retailerConfigurationHelper->getPortalUrl($storeId);
                    $result  = array('error' => 0, 'success' => 1, 'message' => '', 'redirecturl' => $portalUrl);
                    $this->_checkoutSession->unsDividebuyPhoneOrderToken();
                    $this->_checkoutSession->unsDividebuyCheckoutSession();
                }else{
                    $result  = array('error' => 0, 'success' => 1, 'message' => '', 'redirecturl' => $this->_storeManager->getStore()->getBaseUrl());
                }
                
            }else{
                $result  = array('error' => 0, 'success' => 1, 'message' => '', 'redirecturl' => $redirectUrl);
            }

            $this->_onepage->getQuote()->save();
            //restore cart for adding the non dividebuy products
            $this->_checkoutSession->restoreQuote();
            $quoteItemArray = $this->_checkoutConfigBlock->getItemArray();
            $dividebuyIds   = $quoteItemArray['dividebuyIds'];
            foreach ($dividebuyIds as $id) {
                // echo $id;
                // $this->_cartModel->removeItem($id)->save();
                $quoteItem = $this->_quoteItem->load($id);
                $quoteItem->delete();
            }
            $this->_paymentHelper->_prepareDataJSON($result);

        } catch (\Exception $e) {
            // Mage::logException($e);
            if ($this->_checkoutSession->getTemparoryCart()) {
                $this->_checkoutConfigHelper->addSessionProducts();
            }
            $response = array('error' => 1, 'success' => 0, 'message' => $e->getMessage(), 'redirecturl' => '', 'carturl' => $this->_checkoutConfigBlock->getUrl('checkout/cart', ['_secure' => true]));
            /*For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($errorLogStatus == 1) {
                $dataResponse = "============\n";
                $dataResponse .= "Url : Create order postalcode false \n";
                $dataResponse .= "Error message :" . json_encode($e->getMessage()) . "\n";
                $dataResponse .= "Response :" . json_encode($response) . "\n";
                $this->_divideBuylogger->info($dataResponse);
            }
            /*error log end*/
            $this->_paymentHelper->_prepareDataJSON($response);
        }
    }
}
