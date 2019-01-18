<?php
namespace Dividebuy\Payment\Model\Sales;

class Redirect extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Contains order details
     *
     * @var array
     */
    protected $_orderDetails = array();

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Dividebuy\CheckoutConfig\Block\Cart
     */
    protected $_checkoutConfigHelper;

    /**
     * Dividebuy\RetailerConfig\Block\Data
     */
    protected $_retailerConfigHelper;

    /**
     * @var \Magento\Core\Model\Store
     */
    protected $_coreStoreModel;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\Order                         $orderModel
     * @param \Magento\Catalog\Model\Product                     $productModel
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper
     * @param \Dividebuy\CheckoutConfig\Helper\Data              $checkoutConfigHelper
     * @param \Dividebuy\RetailerConfig\Helper\Data              $retailerConfigHelper
     * @param \Magento\Store\Model\StoreManagerInterface         $coreStoreModel
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Dividebuy\CheckoutConfig\Helper\Data $checkoutConfigHelper,
        \Dividebuy\RetailerConfig\Helper\Data $retailerConfigHelper,
        \Magento\Store\Model\StoreManagerInterface $coreStoreModel
    ) {
        $this->_orderModel           = $orderModel;
        $this->_scopeConfig          = $scopeConfig;
        $this->_checkoutConfigHelper = $checkoutConfigHelper;
        $this->_retailerConfigHelper = $retailerConfigHelper;
        $this->_coreStoreModel       = $coreStoreModel;
        $this->_productModel         = $productModel;
        $this->_jsonHelper           = $jsonHelper;
    }

    /**
     * Used to get request which is to be send as a CURL request
     *
     * @param  integer $orderId
     * @return mixed
     */
    public function getRequest($orderId = null)
    {
        if ($orderId) {
            $order                                                           = $this->_orderModel->load($orderId);
            $storeId                                                         = $order->getStoreId();
            $this->_orderDetails['order_detail']['store_order_id']           = $order->getId();
            $this->_orderDetails['order_detail']['store_order_increment_id'] = $order->getIncrementId();
            $this->_orderDetails['order_detail']['order_status'] = $order->getStatus();
            $this->_orderDetails['order_detail']['order_state'] = $order->getState();

            $tokenNumber = $this->_retailerConfigHelper->getTokenNumber($storeId);
            $authNumber  = $this->_retailerConfigHelper->getAuthNumber($storeId);
            if ($tokenNumber && $authNumber) {
                $this->_orderDetails['order_detail']['store_token']          = $tokenNumber;
                $this->_orderDetails['order_detail']['store_authentication'] = $authNumber;
            }

            $excludeKeys = array('entity_id', 'customer_address_id', 'quote_address_id', 'region_id', 'customer_id', 'address_type');

            // Check the order items whether they contain product type other than virtual or downloadable
            $checkItemStatus = $this->checkItemsOfOrder($order);

            $orderBillingAddress = $order->getBillingAddress()->getData();

            // Setting billing address as shipping address if checkItemStatus is 0
            if ($checkItemStatus) {
                $orderShippingAddress = $order->getShippingAddress()->getData();
            } else {
                $orderShippingAddress = $order->getBillingAddress()->getData();
            }

            $orderBillingAddressFiltered  = array_diff_key($orderBillingAddress, array_flip($excludeKeys));
            $orderShippingAddressFiltered = array_diff_key($orderShippingAddress, array_flip($excludeKeys));
            $addressDifference            = array_diff($orderBillingAddressFiltered, $orderShippingAddressFiltered);
            // billing and shipping addresses are different
            if ($addressDifference) {
                $differentAddress = 1;
            } else {
                $differentAddress = 0;
            }

            $this->_orderDetails['order_detail']['logo_url']            = $this->_retailerConfigHelper->getRetailerLogoUrl();
            $this->_orderDetails['order_detail']['grand_total']         = (float) $this->_roundVal($order->getGrandTotal());
            $this->_orderDetails['order_detail']['subtotal']            = (float) $this->_roundVal($order->getSubtotal());
            $this->_orderDetails['order_detail']['subtotalInclVat']     = (float) $this->_roundVal($order->getSubtotalInclTax());
            $this->_orderDetails['order_detail']['discount']            = (float) $this->_roundVal($order->getDiscountAmount());
            $this->_orderDetails['order_detail']['discountApplied']     = $this->getDiscountVatStatus($storeId);
            $this->_orderDetails['order_detail']['shipping']            = (float) $this->_roundVal($order->getShippingAmount());
            $this->_orderDetails['order_detail']['shippingInclVat']     = (float) $this->_roundVal($order->getShippingInclTax());
            $this->_orderDetails['order_detail']['shipping_label']      = $order->getShippingDescription();
            $this->_orderDetails['order_detail']['is_shipping_default'] = $differentAddress;
            $this->_orderDetails['order_detail']['is_billing_default']  = $differentAddress;
            $this->_orderDetails['order_detail']['vat']                 = (float) $this->_roundVal($order->getTaxAmount());
            $this->_orderDetails['product_details']                     = $this->getProductDetails($order);
            $this->_orderDetails['shipping_address']                    = $this->getAddress($order);
            $this->_orderDetails['billing_address']                     = $this->getAddress($order, 'billing');

            $request = $this->_jsonHelper->jsonEncode($this->_orderDetails);

            return $request;
        }
    }

    /**
     * Used get address details of an order
     *
     * @param  object $order
     * @param  string $type
     * @return array
     */
    public function getAddress($order = null, $type = 'shipping')
    {
        if ($type == "billing") {
            $address = $order->getBillingAddress();
        } else {
            // Check the order items whether they contain product type other than virtual or downloadable
            $checkItemStatus = $this->checkItemsOfOrder($order);

            // Setting billing address as shipping address if checkItemStatus is 0
            if ($checkItemStatus) {
                $address = $order->getShippingAddress();
            } else {
                $address = $order->getBillingAddress();
            }

        }

        $addressArray = array();

        $addressArray['first_name'] = $address->getFirstname();
        $addressArray['last_name']  = $address->getLastname();
        $addressArray['email']      = "";
        if ($address->getEmail() != "example@example.com") {
            $addressArray['email'] = $address->getEmail();
        }
        $addressArray['street']   = $address->getStreet();
        $addressArray['postcode'] = $address->getPostcode();
        $addressArray['region']   = $address->getRegion();
        $addressArray['city']     = $address->getCity();

        return $addressArray;
    }

    /**
     * Used to get order details
     *
     * @param  object $order
     * @return array
     */
    public function getProductDetails($order = null)
    {
        $productDetails = array();
        $productCount   = 0;
        $storeId        = $order->getStoreId();
        $globalTaxClass = $this->_scopeConfig->getValue('dividebuy/global/tax_class', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // $taxFlag        = false;
        // if (is_numeric($globalTaxClass)) {
        //     $taxFlag = true;
        // }

        $orderItems = $order->getallVisibleItems();
        foreach ($orderItems as $item) {
            $productId                                        = $item->getProductId();
            $productDetails[$productCount]['name']            = $item->getName();
            $productDetails[$productCount]['sku']             = $item->getSku();
            $productDetails[$productCount]['qty']             = $item->getQtyOrdered();
            $productDetails[$productCount]['price']           = (float) $item->getPrice();
            $productDetails[$productCount]['priceInclVat']    = (float) $item->getPriceInclTax();
            $productDetails[$productCount]['rowTotal']        = (float) $item->getRowTotal();
            $productDetails[$productCount]['rowTotalInclVat'] = (float) $item->getRowTotalInclTax();
            $productDetails[$productCount]['discount']        = (float) $item->getDiscountAmount();

            $productData = $this->_productModel->load($productId);
            if (!empty($productData->getShortDescription())) {
                $productDetails[$productCount]['short_description'] = $productData->getShortDescription();
            } elseif (!empty($productData->getDescription())) {
                $productDetails[$productCount]['short_description'] = $productData->getDescription();
            } else {
                $productDetails[$productCount]['short_description'] = $item->getName();
            }

            $productDetails[$productCount]['product_type']       = $productData->getTypeId();
            $productDetails[$productCount]['product_weight']     = $productData->getWeight();
            $productDetails[$productCount]['product_visibility'] = $productData->getVisibility();

            $productTaxClassOptionId                 = $productData->getDividebuyTaxClass();
            $productDetails[$productCount]['DivVat'] = 0;
            $productTaxClassId                       = $productData->getTaxClassId();
            $productTaxClass                         = $item->getTaxPercent();

            if (!empty($productTaxClassOptionId)) {
                $productTaxClassOptionValue              = $productData->getAttributeText('dividebuy_tax_class');
                $taxClass                                = explode('-', $productTaxClassOptionValue);
                $taxValue                                = trim(str_replace('%', '', $taxClass[1]));
                $productDetails[$productCount]['DivVat'] = $taxValue;
            } elseif (!empty($globalTaxClass)) {
                $productDetails[$productCount]['DivVat'] = $globalTaxClass;
            } else {
                $productDetails[$productCount]['DivVat'] = $productTaxClass;
            }

            $productDetails[$productCount]['image_url'] = $this->getProductImageUrl($productData->getThumbnail());

            $productOptions = $item->getProductOptions();
            if (isset($productOptions['attributes_info'])) {
                $attribute = array();
                foreach ($productOptions['attributes_info'] as $option) {
                    $attribute[$option['label']] = $option['value'];
                }
                $productDetails[$productCount]['product_options'] = $attribute;
            }
            $productCount++;
        }
        return $productDetails;
    }

    /**
     * Get order total details
     *
     * @param  object $order
     * @return array
     */
    public function getOrderTotal($order = null)
    {
        $totalArray                = array();
        $totalArray['grand_total'] = $this->_roundVal((float) $order->getGrandTotal());
        $totalArray['subtotal']    = (float) $this->_roundVal($order->getSubtotal());
        $totalArray['discount']    = (float) $this->_roundVal($order->getDiscountAmount());
        $totalArray['shipping']    = (float) $this->_roundVal($order->getShippingAmount());
        $totalArray['vat']         = (float) $this->_roundVal($order->getTaxAmount());

        return $totalArray;
    }
    /**
     * Used to retrieve product image URL
     *
     * @param  string $imageThumbName
     * @return string
     */
    public function getProductImageUrl($imageThumbName)
    {
        $imageUrl        = $this->_checkoutConfigHelper->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "/" . "catalog/product" . $imageThumbName;
        $imageDirectory  = $this->_checkoutConfigHelper->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "/" . "dividebuy" . "/" . "product" . "/" . "images" . "/" . $imageThumbName;
        $productImageUrl = $this->_checkoutConfigHelper->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "/" . "dividebuy" . "/" . "product" . "/" . "images" . "/" . $imageThumbName;

        if (!is_dir($imageDirectory)) {
            mkdir($imageDirectory, 0777);
        }

        if (!file_exists($imageDirectory) && file_exists($imageUrl)) {
            $imageObj = new Varien_Image($imageUrl);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(135, 135);
            $imageObj->save($imageDirectory);
        }
        return $imageUrl;
    }

    /**
     * Used to round up the product price
     *
     * @param  integer $value
     * @return integer
     */
    protected function _roundVal($value = null)
    {
        // return $this->_coreStoreModel->roundPrice($value);
        return $value;
    }

    /**
     * Decides whether vat is applied before or after Vat
     *
     * @param  integer $storeId
     * @return string
     */
    public function getDiscountVatStatus($storeId = null)
    {
        if ($this->_scopeConfig->getValue('tax/calculation/discount_tax', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return "afterVat";
        } else {
            return "beforeVat";
        }

    }

    /**
     * Returns 1 if order contains products with type other than virtual and downloadable else 0
     *
     * @param $order
     * @return integer
     */
    public function checkItemsOfOrder($order)
    {
        $flag       = 0;
        $orderItems = $order->getallVisibleItems();

        // Creating array of order items product type
        foreach ($orderItems as $items) {
            $productType[] = $items->getData("product_type");
        }

        // Checking the occurrence of simple, configurable, bundle and grouped product type
        if (array_intersect($productType, array('simple', 'configurable', 'bundle', 'grouped'))) {
            $flag = 1;
        }
        return $flag;
    }
}
