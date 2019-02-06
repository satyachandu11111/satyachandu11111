<?php

namespace JustShout\Gfs\Helper;

use JustShout\Gfs\Logger\Logger;
use JustShout\Gfs\Model\Config\Source;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Helper
 *
 * @package   JustShout\Gfs
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2018
 */
class Config extends Data
{
    /**
     * Active Config
     */
    const CONFIG_ACTIVE = 'carriers/gfs/active';

    /**
     * Retailer Id
     */
    const CONFIG_RETAILER_ID = 'carriers/gfs/retailer_id';

    /**
     * Retailer Secret
     */
    const CONFIG_RETAILER_SECRET = 'carriers/gfs/retailer_secret';

    /**
     * Method Title
     */
    const CONFIG_METHOD_TITLE = 'carriers/gfs/title';

    /**
     * Method Name
     */
    const CONFIG_METHOD_NAME = 'carriers/gfs/name';

    /**
     * Delivery Types
     */
    const CONFIG_DELIVERY_TYPES = 'carriers/gfs/delivery_types';

    /**
     * Allowed Countries
     */
    const CONFIG_ALLOWED_COUNTRIES = 'carriers/gfs/allowed_countries';

    /**
     * Primary Colour
     */
    const CONFIG_COLOR_PRIMARY = 'carriers/gfs/color_primary';

    /**
     * Secondary Colour
     */
    const CONFIG_COLOR_SECONDARY = 'carriers/gfs/color_secondary';

    /**
     * Tertiary Colour
     */
    const CONFIG_COLOR_TERTIARY = 'carriers/gfs/color_tertiary';

    /**
     * Customer Fields - Customer Account
     */
    const CONFIG_CUSTOM_FIELDS_CUSTOMER = 'carriers/gfs/custom_fields_customer';

    /**
     * Customer Fields - Order Items
     */
    const CONFIG_CUSTOM_FIELDS_ITEMS = 'carriers/gfs/custom_fields_items';

    /**
     * Get Standard Delivery Title
     */
    const CONFIG_STANDARD_DELIVERY_TITLE = 'carriers/gfs/standard_delivery_title';

    /**
     * Get Calendar Delivery Title
     */
    const CONFIG_CALENDAR_DELIVERY_TITLE = 'carriers/gfs/calendar_delivery_title';

    /**
     * Get Drop Point Delivery Title
     */
    const CONFIG_DROP_POINT_DELIVERY_TITLE = 'carriers/gfs/drop_point_title';

    /**
     * Get Service Sort Order
     */
    const CONFIG_SERVICE_SORT_ORDER = 'carriers/gfs/service_sort_order';

    /**
     * Get Map Icon
     */
    const CONFIG_MAP_HOME_ICON = 'carriers/gfs/map_home_icon';

    /**
     * Get Use Stores
     */
    const CONFIG_USE_STORES = 'carriers/gfs/use_stores';

    /**
     * Get Use DropPoint Stores
     */
    const CONFIG_USE_DROPPOINT_STORES = 'carriers/gfs/use_droppoints_stores';

    /**
     * Get Use Standard
     */
    const CONFIG_USE_STANDARD = 'carriers/gfs/use_standard';

    /**
     * Get Use Drop Points
     */
    const CONFIG_USE_DROPPOINTS = 'carriers/gfs/use_droppoints';

    /**
     * Get Use Calendar
     */
    const CONFIG_USE_CALENDAR = 'carriers/gfs/use_calendar';

    /**
     * Default Service
     */
    const CONFIG_DEFAULT_SERVICE = 'carriers/gfs/default_service';

    /**
     * Default Carrier
     */
    const CONFIG_DEFAULT_CARRIER = 'carriers/gfs/default_carrier';

    /**
     * Default Carrier Code
     */
    const CONFIG_DEFAULT_CARRIER_CODE = 'carriers/gfs/default_carrier_code';

    /**
     * Default Price
     */
    const CONFIG_DEFAULT_PRICE = 'carriers/gfs/default_price';

    /**
     * Default Min Delivery Time
     */
    const CONFIG_DEFAULT_MIN_DELIVERY_TIME = 'carriers/gfs/default_min_delivery_time';

    /**
     * Default Max Delivery Time
     */
    const CONFIG_DEFAULT_MAX_DELIVERY_TIME = 'carriers/gfs/default_max_delivery_time';

    /**
     * Encryptor
     *
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * Json
     *
     * @var Json
     */
    protected $_json;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $_logger;

    /**
     * Config constructor
     *
     * @param Context            $context
     * @param Json               $json
     * @param EncryptorInterface $encryptor
     * @param Logger             $logger
     */
    public function __construct(
        Context            $context,
        Json               $json,
        EncryptorInterface $encryptor,
        Logger             $logger
    ) {
        parent::__construct($context, $json);
        $this->_encryptor = $encryptor;
        $this->_logger = $logger;
    }

    /**
     * Check if module is active
     *
     * @return string
     */
    public function isActive()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Retailer Id
     *
     * @return string
     */
    public function getRetailerId()
    {
        $retailerId = $this->scopeConfig->getValue(self::CONFIG_RETAILER_ID, ScopeInterface::SCOPE_STORE);

        return $this->_encryptor->decrypt($retailerId);
    }

    /**
     * Get Retailer Secret
     *
     * @return string
     */
    public function getRetailerSecret()
    {
        $retailerSecret = $this->scopeConfig->getValue(self::CONFIG_RETAILER_SECRET, ScopeInterface::SCOPE_STORE);

        return $this->_encryptor->decrypt($retailerSecret);
    }

    /**
     * Get Shipping Method Title
     * 
     * @return string
     */
    public function getMethodTitle()
    {
        $title = $this->scopeConfig->getValue(self::CONFIG_METHOD_TITLE, ScopeInterface::SCOPE_STORE);
        if (!$title) {
            $title = __('Just Shout GFS Delivery');
        }

        return $title;
    }

    /**
     * Get Shipping Method Name
     *
     * @return string
     */
    public function getMethodName()
    {
        $name = $this->scopeConfig->getValue(self::CONFIG_METHOD_NAME, ScopeInterface::SCOPE_STORE);
        if (!$name) {
            $name = __('Just Shout GFS Delivery');
        }

        return $name;
    }

    /**
     * Get Enabled Delivery Types
     *
     * @return array
     */
    public function getDeliveryTypes()
    {
        $types = array_filter(explode(',', $this->scopeConfig->getValue(self::CONFIG_DELIVERY_TYPES, ScopeInterface::SCOPE_STORE)));
        $allowedTypes = [
            Source\DeliveryTypes::METHOD_STANDARD,
            Source\DeliveryTypes::METHOD_DROP_POINT,
            Source\DeliveryTypes::METHOD_STORE
        ];

        return array_filter($types, function ($key) use ($allowedTypes) {
            return in_array($key, $allowedTypes);
        });
    }

    /**
     * Get Allowed Countries
     *
     * @return array
     */
    public function getAllowedCountries()
    {
        $allowedCountries = $this->scopeConfig->getValue(self::CONFIG_ALLOWED_COUNTRIES, ScopeInterface::SCOPE_STORE);
        $allowedCountries = explode(',', $allowedCountries);
        $allowedCountries = array_filter($allowedCountries);

        return $allowedCountries;
    }

    /**
     * Get Primary Color
     *
     * @return string
     */
    public function getColorPrimary()
    {
        $hex = $this->scopeConfig->getValue(self::CONFIG_COLOR_PRIMARY, ScopeInterface::SCOPE_STORE);
        $hex = '#' . ltrim($hex, '#');
        if (!preg_match('/^#[a-f0-9]{6}$/i', $hex)) {
            $hex = '#B20000';
        }

        return $hex;
    }

    /**
     * Get Secondary Color
     *
     * @return string
     */
    public function getColorSecondary()
    {
        $hex = $this->scopeConfig->getValue(self::CONFIG_COLOR_SECONDARY, ScopeInterface::SCOPE_STORE);
        $hex = '#' . ltrim($hex, '#');
        if (!preg_match('/^#[a-f0-9]{6}$/i', $hex)) {
            $hex = '#FFFFFF';
        }

        return $hex;
    }

    /**
     * Get Tertiary Color
     *
     * @return string
     */
    public function getColorTertiary()
    {
        $hex = $this->scopeConfig->getValue(self::CONFIG_COLOR_TERTIARY, ScopeInterface::SCOPE_STORE);
        $hex = '#' . ltrim($hex, '#');
        if (!preg_match('/^#[a-f0-9]{6}$/i', $hex)) {
            $hex = '#000000';
        }

        return $hex;
    }

    /**
     * Get custom fields for customers
     *
     * @return array
     */
    public function getCustomerCustomFields()
    {
        try {
            $fields = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FIELDS_CUSTOMER, ScopeInterface::SCOPE_STORE);
            if (!$fields) {
                throw new \Exception();
            }
            $fields = $this->_json->unserialize($fields);
            $fields = array_values($fields);
        } catch (\InvalidArgumentException $e) {
            $this->_logger->debug('Issue retrieving customer custom fields. Please resolve.');
            $fields = [];
        } catch (\Exception $e) {
            $this->_logger->debug('Issue retrieving customer custom fields. Please resolve.');
            $fields = [];
        }

        return $fields;
    }

    /**
     * Get Custom Field for customers by its number
     *
     * @param int $number
     *
     * @return array
     */
    public function getCustomerCustomField($number)
    {
        $fields = $this->getCustomerCustomFields();
        $key = (int) $number - 1;

        return isset($fields[$key]) ? $fields[$key] : [];
    }

    /**
     * Get custom fields for item/product
     *
     * @return array
     */
    public function getItemCustomFields()
    {
        try {
            $fields = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FIELDS_ITEMS, ScopeInterface::SCOPE_STORE);
            if (!$fields) {
                throw new \Exception();
            }
            $fields = $this->_json->unserialize($fields);
            $fields = array_values($fields);
        } catch (\InvalidArgumentException $e) {
            $this->_logger->debug('Issue retrieving item custom fields. Please resolve.');
            $fields = [];
        } catch (\Exception $e) {
            $this->_logger->debug('Issue retrieving customer custom fields. Please resolve.');
            $fields = [];
        }

        return $fields;
    }

    /**
     * Get Custom Field for item/product by its number
     *
     * @param int $number
     *
     * @return array
     */
    public function getItemCustomField($number)
    {
        $fields = $this->getItemCustomFields();
        $key = (int) $number - 1;

        return isset($fields[$key]) ? $fields[$key] : [];
    }

    /**
     * Get Standard Delivery Title
     *
     * @return string
     */
    public function getStandardDeliveryTitle()
    {
        $title = trim($this->scopeConfig->getValue(self::CONFIG_STANDARD_DELIVERY_TITLE, ScopeInterface::SCOPE_STORE));
        if (!$title) {
            $title = __('Standard Delivery');
        }

        return $title;
    }

    /**
     * Get Calendar Delivery Title
     *
     * @return string
     */
    public function getCalendarDeliveryTitle()
    {
        $title = trim($this->scopeConfig->getValue(self::CONFIG_CALENDAR_DELIVERY_TITLE, ScopeInterface::SCOPE_STORE));
        if (!$title) {
            $title = __('Choose a delivery date and time');
        }

        return $title;
    }

    /**
     * Get Drop Point Delivery Title
     *
     * @return string
     */
    public function getDropPointTitle()
    {
        $title = trim($this->scopeConfig->getValue(self::CONFIG_DROP_POINT_DELIVERY_TITLE, ScopeInterface::SCOPE_STORE));
        if (!$title) {
            $title = __('Collect your order');
        }

        return $title;
    }

    /**
     * Get Service Sort Order
     *
     * @return string
     */
    public function getServiceSortOrder()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SERVICE_SORT_ORDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Map Home Icon
     *
     * @return string|null
     */
    public function getMapHomeIcon()
    {
        $icon = $this->scopeConfig->getValue(self::CONFIG_MAP_HOME_ICON, ScopeInterface::SCOPE_STORE);
        if (!$icon) {
            return null;
        }

        return $this->_urlBuilder->getBaseUrl(['_type' => 'media']) . 'gfs/map/icons/' . $icon;
    }

    /**
     * Get Use Stores
     *
     * @return bool
     */
    public function getUseStores()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_USE_STORES, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Use DropPoint Stores
     *
     * @return bool
     */
    public function getUseDropPointStores()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_USE_DROPPOINT_STORES, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Use Standard
     *
     * @return bool
     */
    public function getUseStandard()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_USE_STANDARD, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Use Drop Points
     *
     * @return bool
     */
    public function getUseDropPoints()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_USE_DROPPOINTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Use Calendar
     *
     * @return bool
     */
    public function getUseCalendar()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_USE_CALENDAR, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Service
     *
     * @return string
     */
    public function getDefaultService()
    {
        return $this->scopeConfig->getValue(self::CONFIG_DEFAULT_SERVICE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Carrier
     *
     * @return string
     */
    public function getDefaultCarrier()
    {
        return $this->scopeConfig->getValue(self::CONFIG_DEFAULT_CARRIER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Carrier Code
     *
     * @return string
     */
    public function getDefaultCarrierCode()
    {
        return $this->scopeConfig->getValue(self::CONFIG_DEFAULT_CARRIER_CODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Price
     *
     * @return float
     */
    public function getDefaultPrice()
    {
        return (float) $this->scopeConfig->getValue(self::CONFIG_DEFAULT_PRICE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Min Delivery Time
     *
     * @return int
     */
    public function getDefaultMinDeliveryTime()
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_DEFAULT_MIN_DELIVERY_TIME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Max Delivery Time
     *
     * @return int
     */
    public function getDefaultMaxDeliveryTime()
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_DEFAULT_MAX_DELIVERY_TIME, ScopeInterface::SCOPE_STORE);
    }
}
