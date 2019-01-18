<?php
namespace Dividebuy\CheckoutConfig\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CART_ENABLED               = 'dividebuy/cart/enabled';
    const XML_PATH_CART_POSITION              = 'dividebuy/cart/position';
    const XML_PATH_CART_BUTTON_IMAGE          = 'dividebuy/cart/button_image';
    const XML_PATH_CART_BUTTON_HOVER          = 'dividebuy/cart/button_image_hover';
    const DIVIDEBUY_MEDIA_DIR                 = 'dividebuy/';
    const XML_PATH_CART_BUTTON_CSS            = 'dividebuy/cart/customcss';
    const XML_PATH_CART_PREFIX_LABEL          = 'dividebuy/cart/btnprefixlbl';
    const XML_PATH_CART_PREFIX_CSS            = 'dividebuy/cart/btnprefixcss';
    const XML_PATH_CART_SUFFIX_LABEL          = 'dividebuy/cart/btnsuffixlbl';
    const XML_PATH_CART_SUFFIX_CSS            = 'dividebuy/cart/btnsuffixcss';
    const XML_PATH_DIVIDEBUY_EXCLUDEPOSTCODE  = 'dividebuy/global/exclude_post_codes';
    const NON_DELIVERABLE_POSTCODE_MSG        = 'Unfortunately we are unable to deliver to this postcode. Please call our Customer Service Team for more details: 0800 085 0885.';
    const XML_PATH_ORDER_MIN_AMOUNT           = 'dividebuy/global/min_order';
    const XML_PATH_DIVIDEBUY_COURIERS         = 'dividebuy/global/couriers';
    const XML_PATH_DIVIDEBUY_EXTENSION_STATUS = 'dividebuy/general/extension_status';

    /**
     * Contains all dividebuy products detail
     * @var array
     */
    protected static $divideBuyItems;

    /**
     * Contains all non dividebuy products detail
     * @var array
     */
    protected static $otherItems;

    /**
     * Currently selected store ID if applicable
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Dividebuy\CheckoutConfig\Model\CartModel
     */
    protected $_divideBuyCartModel;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Contains the details of current cart items
     * @var array
     */
    protected $_cartSplit = [];

    /**
     * Contains the details of all dividebuy products of current cart
     * @var array
     */
    protected $_divideBuy = [];

    /**
     * Contains the details of all non dividebuy products of current cart
     * @var array
     */
    protected $_nonDivideBuy = [];

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteloader;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Checkout\Model\Session
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Dividebuy\CheckoutConfig\Model\CartModel
     * @param \Dividebuy\RetailerConfig\Helper\Data
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Model\CartModel $divideBuyCartModel,
        \Dividebuy\RetailerConfig\Helper\Data $retailerConfigHelper,
        \Magento\Framework\Serialize\Serializer\Json $serialize
    ) {
        $this->_storeManager       = $storeManager;
        $this->_checkoutSession    = $checkoutSession;
        $this->_productloader      = $productloader;
        $this->_quoteloader        = $quoteFactory;
        $this->_jsonHelper         = $jsonHelper;
        $this->_divideBuyCartModel = $divideBuyCartModel;
        $this->serialize           = $serialize;
        parent::__construct($context);
    }

    /**
     * Retrieves the store Id
     * @return integer
     */
    public function getStoreId()
    {
        if (!$this->_storeId) {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieves the button css value from configuration
     * @param  integer $storeId
     * @return integer
     */
    public function getButtonCss($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_BUTTON_CSS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves couriers from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getCouriers($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DIVIDEBUY_COURIERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves prefix label from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getPrefixLabel($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_PREFIX_LABEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves prefix css from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getPrefixCss($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_PREFIX_CSS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves suffix label from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getSuffixLabel($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_SUFFIX_LABEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves suffix label from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getSuffixCss($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_SUFFIX_CSS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves cart position from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getCartPosition($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves cart status from configuration
     * @param  integer $storeId
     * @return string
     */
    public function getCartStatus($storeId = null)
    {
        $isIPAllowed              = $this->isIPAllowed($storeId);
        $isDividebuyPaymentActive = $this->scopeConfig
            ->getValue(
                "payment/dbpayment/active",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        $cartStatus = $this->scopeConfig
            ->getValue(
                self::XML_PATH_CART_ENABLED,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

        $extensionStatus = $this->scopeConfig
            ->getValue(
                self::XML_PATH_DIVIDEBUY_EXTENSION_STATUS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

        if ($cartStatus && $isDividebuyPaymentActive && $isIPAllowed && $extensionStatus) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves button image url from configuration
     * @return string
     */
    public function getButtonImageUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::DIVIDEBUY_MEDIA_DIR . $this->_getButtonImage();
    }

    /**
     * Retrieves button image from configuration
     * @param  integer $storeId
     * @return string
     */
    protected function _getButtonImage($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_BUTTON_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves button hover image url from configuration
     * @return string
     */
    public function getButtonHoverImageUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::DIVIDEBUY_MEDIA_DIR . $this->_getButtonHoverImage();
    }

    /**
     * Retrieves button hover image from configuration
     * @param  integer $storeId
     * @return string
     */
    protected function _getButtonHoverImage($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART_BUTTON_HOVER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get current url for store
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
    }

    /**
     * Get Store name
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get base URL
     * @param  string $scope
     * @return string
     */
    public function getBaseUrl($scope = null)
    {
        return $this->_storeManager->getStore()->getBaseUrl($scope);
    }

    /**
     * Get list of postcodes in which DivideBuy shipment is not available.
     * @return string
     */
    /**
     * Checks whether the given postcode is deliverable or not
     * @param  string $zipcode
     * @return boolean
     */
    public function getDividebuyPostcodes($zipcode)
    {
        $postCodes = $this->scopeConfig->getValue(
            self::XML_PATH_DIVIDEBUY_EXCLUDEPOSTCODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!empty($postCodes)) {
            $nonDeliverableZipCodes = explode(',', $postCodes);
            $zipcode                = strtolower(trim($zipcode));
            $zipcode                = str_replace(' ', '', $zipcode);
            for ($i = 0; $i < sizeof($nonDeliverableZipCodes); $i++) {
                $nonDeliverableZipCodes[$i] = strtolower(trim($nonDeliverableZipCodes[$i]));
                $nonDeliverableZipCodes[$i] = str_replace(' ', '', $nonDeliverableZipCodes[$i]);
                $postCodeMatchLength        = strlen($nonDeliverableZipCodes[$i]);
                $zipCodeMatch               = substr($zipcode, 0, $postCodeMatchLength);
                if ($zipCodeMatch == $nonDeliverableZipCodes[$i]) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Displaying error message if DivideBuy shipment is not available for particular postcodes.
     * @return string
     */
    public function showPostcodeMsg()
    {
        return self::NON_DELIVERABLE_POSTCODE_MSG;
    }

    /**
     * Retrieves minimum order amount from configuration
     * @param  integer $storeId
     * @return integer
     */
    public function getMinOrderAmount($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_MIN_AMOUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Used to load product by id
     *
     * @param  int $id
     * @return object
     */
    public function loadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    /**
     * Used to load quote by id
     *
     * @param  int $id
     * @return object
     */
    public function loadQuote($id)
    {
        return $this->_quoteloader->create()->load($id);
    }

    /**
     * Used to get array of cart items with the count of dividebuy and non-dividebuy product
     *
     * @param  int $quoteId
     * @return mixed
     */
    public function getDividebuyItemArray($quoteId = null)
    {
        if ($this->_cartSplit) {
            return $this->_cartSplit;
        }
        $carts = $this->_checkoutSession->getQuote()->getAllItems();

        if (count($carts) <= 0) {
            if ($quoteId) {
                $quote = $this->loadQuote($quoteId);
                $carts = $quote->getAllItems();
            } else {
                return 'no-items';
            }
        }

        // Getting details of DivideBuy and non DivideBuy items.
        $itemCount = 0;
        foreach ($carts as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $itemCount++;
            $product = $this->loadProduct($item->getProductId());
            if ($product->getDividebuyEnable()) {
                $this->_divideBuy[]    = $item;
            } else {
                $this->_nonDivideBuy[] = $item;
            }
        }
        return $this->_itemArray($itemCount);
    }

    /**
     * Used to get array of cart items with the count of dividebuy and non-dividebuy product
     *
     * @param  int $itemCount
     * @return array
     */
    protected function _itemArray($itemCount = null)
    {
        $this->_cartSplit['cart_item'] = $itemCount;

        self::$divideBuyItems = $this->_divideBuy;
        self::$otherItems     = $this->_nonDivideBuy;

        // Getting count of DivideBuy and non DivideBuy products.
        $this->_cartSplit['dividebuy']    = count($this->_divideBuy);
        $this->_cartSplit['nodividebuy']  = count($this->_nonDivideBuy);
        return $this->_cartSplit;
    }

    /**
     * Used to add products in to the cart which were removed from cart and added to the checkout session
     */
    public function addSessionProducts()
    {
        //Getting value of current session for cart products.
        $sessionArray = $this->_checkoutSession->getTemparoryCart();
        $this->_checkoutSession->unsTemparoryCart();
        foreach ($sessionArray as $item) {
            $productType     = $item['product_type'];
            $itemInfoRequest = $this->serialize->unserialize($item['info_byRequest']);

            // Checking product types and based on that adding respected item values to cart.
            if ($productType == "simple") {
                if (!empty($itemInfoRequest['options'])) {
                    $simpleItemParams['options'] = $itemInfoRequest['options'];
                }
                $simpleItemParams['qty'] = $item['qty'];
                $this->_divideBuyCartModel->addProductsInCart($item['product_id'], $simpleItemParams);
            }
            if ($productType == "bundle") {
                $bundleItemParams['bundle_option']     = $itemInfoRequest['bundle_option'];
                $bundleItemParams['bundle_option_qty'] = $itemInfoRequest['bundle_option_qty'];
                $bundleItemParams['qty']               = $itemInfoRequest['qty'];
                $this->_divideBuyCartModel->addProductsInCart($item['product_id'], $bundleItemParams);
            }
            if ($productType == "configurable") {
                $configurableItemParams['super_attribute'] = $itemInfoRequest['super_attribute'];
                $configurableItemParams['qty']             = $itemInfoRequest['qty'];
                $this->_divideBuyCartModel->addProductsInCart($item['product_id'], $configurableItemParams);
            }
            if ($productType == "grouped") {
                $groupedItemParams['super_product_config'] = $itemInfoRequest['super_product_config'];
                $groupedItemParams['qty']                  = $item['qty'];
                $this->_divideBuyCartModel->addProductsInCart($item['product_id'], $groupedItemParams);
            }
            if ($productType == "downloadable") {
                // if (!empty($itemInfoRequest['options'])) {
                //     $downloadableItemParams['options'] = $itemInfoRequest['options'];
                // }
                $downloadableItemParams['qty'] = $itemInfoRequest["qty"];
                $request                       = new \Magento\Framework\DataObject();
                $request->setData($downloadableItemParams);
                $this->_divideBuyCartModel->addProductsInCart($item['product_id'], $request);
            }
            unset($simpleItemParams);
            unset($bundleItemParams);
            unset($configurableItemParams);
            unset($groupedItemParams);
        }
    }

    /**
     * Returns the flag for checkout with coupon code
     *
     * @return int
     */
    public function getCheckoputWithCouponCodeFlag()
    {
        return $this->scopeConfig->getValue(
            "dividebuy/general/allow_checkout_with_coupon",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Used to check whether current ip is allowed to see Dividebuy functionality or not
     *
     * @param  int  $storeId
     * @return boolean
     */
    public function isIPAllowed($storeId = null)
    {
        $serverIP  = $_SERVER['REMOTE_ADDR'];
        $allowedIP = $this->scopeConfig
            ->getValue(
                "dividebuy/general/allowed_ip",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        $allowedIPArray = array_map('trim', explode(",", $allowedIP));
        if (!empty($allowedIPArray) && $allowedIP != '') {
            if (in_array($serverIP, $allowedIPArray)) {
                $isIPAllowed = true;
            } else {
                $isIPAllowed = false;
            }
        } else {
            $isIPAllowed = true;
        }
        return $isIPAllowed;
    }
}
