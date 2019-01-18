<?php
namespace Dividebuy\CheckoutConfig\Block;

use Dividebuy\CheckoutConfig\Helper\Data as CheckoutHelper;
use Dividebuy\RetailerConfig\Helper\Data as RetailerHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Shipping\Model\Config;

class Cart extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Dividebuy\CheckoutConfig\Helper\Data
     */
    protected $_checkoutHelper;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_retailerHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

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
     * @var \Magento\Shipping\Model\Config
     */
    protected $_deliveryModelConfig;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cartModel;

    /**
     * Contains the details which are going be used on Cart page
     * @var array
     */
    protected $_linkData = array();

    /**
     * Contains all non dividebuy products detail
     * @var array
     */
    protected static $otherItems;

    /**
     * Contains all dividebuy products detail
     * @var array
     */
    protected static $divideBuyItems;

    /**
     * Contains the details of current cart items
     * @var array
     */
    protected $_cartSplit = array();

    /**
     * Contains the details of all dividebuy products of current cart
     * @var array
     */
    protected $_divideBuy = array();

    /**
     * Contains the details of all non dividebuy products of current cart
     * @var array
     */
    protected $_nonDivideBuy = array();

    /**
     * Contains the ids of all non dividebuy products of current cart
     * @var array
     */
    protected $_removeIds = array();

    /**
     * Get store config model
     * @var $_config
     */
    protected $_config;

    /**
     * Contains the ids of all dividebuy products of current cart
     * @var array
     */
    protected $_dividebuyIds = array();

    /**
     * Contains the ids of all dividebuy products of current cart
     * @var array
     */
    protected $_cart;

    /**
     * @param Context                                            $context
     * @param CheckoutHelper                                     $checkoutHelper
     * @param RetailerHelper                                     $retailerHelper
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Magento\Catalog\Model\ProductFactory              $productloader
     * @param \Magento\Quote\Model\QuoteFactory                  $quoteFactory
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper
     * @param Config                                             $deliveryModelConfig
     * @param \Magento\Catalog\Helper\Image                      $imageHelper
     * @param \Magento\Framework\Pricing\Helper\Data             $priceHelper
     * @param \Magento\Checkout\Model\Cart                       $cartModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param array                                              $data
     */
    public function __construct(Context $context,
        CheckoutHelper $checkoutHelper,
        RetailerHelper $retailerHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Config $deliveryModelConfig,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        array $data = []) {
        $this->_checkoutHelper      = $checkoutHelper;
        $this->_retailerHelper      = $retailerHelper;
        $this->_checkoutSession     = $checkoutSession;
        $this->_productloader       = $productloader;
        $this->_quoteloader         = $quoteFactory;
        $this->_jsonHelper          = $jsonHelper;
        $this->_imageHelper         = $imageHelper;
        $this->_priceHelper         = $priceHelper;
        $this->_cartModel           = $cartModel;
        $this->_deliveryModelConfig = $deliveryModelConfig;
        $this->serialize            = $serialize;
        parent::__construct($context, $data);
    }

    /**
     * Returns the flag for in store collection logic
     *
     * @return integer $auto_shipping
     */
    public function getAutoCheckoutStatus()
    {
        $storeId       = $this->_retailerHelper->getStoreId();
        $auto_shipping = $this->_scopeConfig->getValue("dividebuy/global/retailor_auto_checkout", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        return $auto_shipping;
    }

    /**
     * Return an instance of \Dividebuy\CheckoutConfig\Helper\Data
     *
     * @return \Dividebuy\CheckoutConfig\Helper\Data
     */
    public function getCheckoutConfigHelper()
    {
        return $this->_checkoutHelper;
    }

    /**
     * Return an instance of \Dividebuy\RetailerConfig\Helper\Data
     *
     * @return \Dividebuy\RetailerConfig\Helper\Data
     */
    public function getRetailerHelper()
    {
        return $this->_retailerHelper;
    }

    /**
     * Return an instance of \Magento\Checkout\Model\Session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * Return an instance of \Magento\Checkout\Model\Session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getPriceHelper()
    {
        return $this->_priceHelper;
    }

    /**
     * Used to check whether the cart contains DivideBuy Product or not
     *
     * @return boolean
     */
    public function checkDivideBuy()
    {
        if ($this->_checkoutHelper->getCartStatus()) {
            return true;
        }
        return false;
    }

    /**
     * Used to load product by id
     *
     * @param  integer $id
     * @return object
     */
    public function loadProduct($id)
    {
        return $this->_productloader->create()->load($id);

    }

    /**
     * Used to load quote by id
     *
     * @param  integer $id
     * @return object
     */
    public function loadQuote($id)
    {
        return $this->_quoteloader->create()->load($id);
    }

    /**
     * Used to get array of cart items with the count of dividebuy and non-dividebuy product
     *
     * @param  integer $quoteId
     * @return mixed
     */
    public function getItemArray($quoteId = null)
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

        $itemCount = 0;
        foreach ($carts as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $itemCount++;
            $product = $this->loadProduct($item->getProductId());
            if ($product->getDividebuyEnable()) {
                $this->_divideBuy[]    = $item;
                $this->_dividebuyIds[] = $item->getId();
            } else {
                $this->_nonDivideBuy[] = $item;
                $this->_removeIds[]    = $item->getId();
            }
        }
        return $this->_itemArray($itemCount);
    }

    /**
     * Used to get array of cart items with the count of dividebuy and non-dividebuy product
     *
     * @param  integer $itemCount
     * @return array
     */
    protected function _itemArray($itemCount = null)
    {
        $jsonData     = $this->_jsonHelper->jsonEncode($this->_removeIds);
        $dividebuyIds = $this->_jsonHelper->jsonEncode($this->_dividebuyIds);

        $this->_cartSplit['cart_item'] = $itemCount;

        self::$divideBuyItems = $this->_divideBuy;
        self::$otherItems     = $this->_nonDivideBuy;

        $this->_cartSplit['dividebuy']    = count($this->_divideBuy);
        $this->_cartSplit['nodividebuy']  = count($this->_nonDivideBuy);
        $this->_cartSplit['remove_ids']   = $jsonData;
        $this->_cartSplit['dividebuyIds'] = $this->_dividebuyIds;
        return $this->_cartSplit;

    }

    /**
     * Used to get the details which are going be used on Cart page
     *
     * @return array
     */
    public function getCartLinkData()
    {
        $storeId                   = $this->_retailerHelper->getStoreId();
        $this->_linkData['status'] = $this->_checkoutHelper->getCartStatus($storeId);
        if ($this->_linkData['status']) {
            $this->_linkData['button_image']       = $this->_checkoutHelper->getButtonImageUrl($storeId);
            $this->_linkData['position']           = $this->_checkoutHelper->getCartPosition($storeId);
            $this->_linkData['button_hover_image'] = $this->_checkoutHelper->getButtonHoverImageUrl($storeId);
            $this->_linkData['custom_css']         = $this->_checkoutHelper->getButtonCss($storeId);
            $this->_linkData['prefix_label']       = $this->_checkoutHelper->getPrefixLabel($storeId);
            $this->_linkData['prefix_css']         = $this->_checkoutHelper->getPrefixCss($storeId);
            $this->_linkData['suffix_label']       = $this->_checkoutHelper->getSuffixLabel($storeId);
            $this->_linkData['suffix_css']         = $this->_checkoutHelper->getSuffixCss($storeId);
            $this->_linkData['retailer_image']     = $this->_retailerHelper->getRetailerLogoUrl($storeId);
            $this->_linkData['store_name']         = $this->_retailerHelper->getStoreName($storeId);
        }
        return $this->_linkData;
    }

    /**
     * Used to get an array of shipping rates
     *
     * @return array
     */
    public function getShippingRates()
    {
        $address = $this->_cartModel->getQuote()->getShippingAddress();
        $rates   = $address->collectShippingRates()
            ->getGroupedAllShippingRates();
        return $rates;
    }

    /**
     * Used to get only dividebuy enabled products with html from current cart
     *
     * @return html
     */
    public function getDivideBuyContent()
    {
        return $this->_dividebuyProducts();
    }

    /**
     * Used to get only non dividebuy products with html from current cart
     *
     * @return html
     */
    public function getNonDivideBuyContent()
    {
        return $this->_nonDividebuyProducts();
    }

    /**
     * Used to get only dividebuy enabled products with html from current cart
     *
     * @return html
     */
    protected function _dividebuyProducts()
    {
        $items = self::$divideBuyItems;

        $grandTotal = 0;
        $html       = ' <ul class="datas"> ';
        foreach ($items as $item) {
            $_product          = $item->getProduct();
            $productTotalPrice = $item->getRowTotal();

            $imgurls = $this->_imageHelper->init($_product, 'cart_page_product_thumbnail')->resize(150, 60)->getUrl();
            $html .= ' <li class="clearfix"> ';
            $html .= ' <div class="row"> ';
            $html .= '<div class="product-image-1 col-sm-3 col-xs-4"><img class="img-responsive img-thumbnail" src="' . $imgurls . '"/></div>';
            $html .= '<div class="modal-content-area col-sm-7 col-xs-6"><div class="pro-name">' . $item->getName() . '</div><span class="price">' . $this->_priceHelper->currency($item->getPriceInclTax(), true, false) . '</span></div></div>';
            $html .= ' </li> ';
            $grandTotal = $grandTotal + $productTotalPrice;
        }
        $html .= ' </ul> ';
        return $html;
    }

    /**
     * Used to get only non dividebuy products with html from current cart
     *
     * @return html
     */
    protected function _nonDividebuyProducts()
    {
        $otherItems = self::$otherItems;

        $html = ' <ul class="datas"> ';
        $count = 1;

        foreach ($otherItems as $item) {
            $_product = $item->getProduct();
            $imgurls  = $this->_imageHelper->init($_product, 'cart_page_product_thumbnail')->resize(150, 60)->getUrl();

            if($count == count($otherItems)) {
                $html .= ' <li class="clearfix" id="last-item-dividebuy" tabindex="0"> ';
            } else {
                $html .= ' <li class="clearfix"> ';
            }
            $html .= '<div class="row">';
            $html .= '<div class="product-image-1 col-sm-3 col-xs-4"><img class="img-responsive img-thumbnail" src="' . $imgurls . '"/></div>';
            $html .= '<div class="modal-content-area col-sm-7 col-xs-6">';
            $html .= '<div class="pro-name">' . $item->getName() . '</div>';
            $html .= '<span class="price">' . $this->_priceHelper->currency($item->getPriceInclTax(), true, false) . '</span>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</li>';
            $count++;
        }
        $html .= ' </ul> ';
        return $html;
    }

    /**
     * Used to remove all non-dividebuy products from the cart before placing the dividebuy order
     */
    public function removeNonDivideBuyProducts()
    {
        $nonDivideBuyProducts = $this->getNonDivideBuyProducts();
        if (count($nonDivideBuyProducts) > 0) {
            // Get all cart products
            $items = $this->_checkoutSession->getQuote()->getAllVisibleItems();

            $nonDivideBuyItems = array();
            $i                 = 0;

            // Foreach cart products, if product is non DivideBuy then storing products in session and remove from cart.

            foreach ($items as $item) {
                if (in_array($item->getItemId(), $nonDivideBuyProducts)) {
                    $nonDivideBuyItems[$i]['item_id'] = $item->getItemId();
                    // Removing products from cart.
                    $this->_cartModel->removeItem($item->getItemId())->save();
                }

                foreach ($item->getOptions() as $option) {
                    if ($option->getCode() == 'info_buyRequest') {
                        if (in_array($item->getItemId(), $nonDivideBuyProducts)) {
                            $data = $this->serialize->unserialize($option->getValue());
                            if (array_key_exists('super_product_config', $data)) {
                                $nonDivideBuyItems[$i]['product_type'] = 'grouped';
                                $nonDivideBuyItems[$i]['qty']          = $item->getQty();

                            } else {
                                $product                               = $this->loadProduct($option->getProductId());
                                $nonDivideBuyItems[$i]['product_type'] = $product->getTypeId();
                            }
                            $nonDivideBuyItems[$i]['qty']            = $item->getQty();
                            $nonDivideBuyItems[$i]['product_id']     = $option->getProductId();
                            $nonDivideBuyItems[$i]['info_byRequest'] = $option->getValue();
                        }
                    }
                }
                $i++;
            }

            // Generating core session for storing non DivideBuy products temporary.
            if ($this->_checkoutSession->getTemparoryCart()) {
                $existingCartSession = $this->_checkoutSession->getTemparoryCart();
                $newCartSession      = array_merge($nonDivideBuyItems, $existingCartSession);
                $this->_checkoutSession->setTemparoryCart($newCartSession);
            } else {
                $this->_checkoutSession->setTemparoryCart($nonDivideBuyItems);
            }
        }
    }

    /**
     * Used to get the grand total of all dividebuy products available in the current cart
     *
     * @return integer
     */
    public function getDivideBuyTotal()
    {
        $items      = self::$divideBuyItems;
        $grandTotal = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                $productTotalPrice = $item->getRowTotalInclTax();
                $productDiscount   = $item->getDiscountAmount();
                $grandTotal        = $grandTotal + $productTotalPrice - $productDiscount;
            }
        }

        return $grandTotal;
    }

    /**
     * Used to get all non dividebuy products with their respective ids from current cart
     *
     * @return array
     */
    public function getNonDivideBuyProducts()
    {
        $nonDivideBuyProducts = array();
        $nonDivideBuyProducts = $this->getItemArray();
        $nonDivideBuyProducts = preg_replace(array('/^\[/', '/\]$/'), '', $nonDivideBuyProducts['remove_ids']);
        $nonDivideBuyProducts = str_replace('"', '', $nonDivideBuyProducts);
        $nonDivideBuyProducts = explode(',', $nonDivideBuyProducts);

        return array_filter($nonDivideBuyProducts);
    }

    /**
     * Used to check that current dividebuy product total is greater than the min order amount or not
     *
     * @return boolean
     */
    public function checkMinOrderAmount()
    {
        if ($this->_checkoutHelper->getMinOrderAmount() > self::getDivideBuyTotal()) {
            return false;
        }
        return true;
    }

    /**
     * Used to get minimum Order amount
     *
     * @return integer
     */
    public function getMinOrderAmount()
    {
        return $this->_checkoutHelper->getMinOrderAmount();
    }

    /**
     * Returns true if checkout with coupon code is enabled.
     *
     * @return boolean $flag
     */
    public function checkoutWithCouponCode()
    {
        $couponCode                 = $this->_checkoutSession->getQuote()->getCouponCode();
        $checkoutWithCouponCodeFlag = $this->_checkoutHelper->getCheckoputWithCouponCodeFlag();

        $flag = 1;
        if (!$checkoutWithCouponCodeFlag && !empty($couponCode) && $couponCode !== "") {
            $flag = 0;
        }

        return $flag;
    }

}
