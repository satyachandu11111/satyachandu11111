<?php

/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */

namespace Jjcommerce\CollectPlus\Block\Onepage;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;
use Jjcommerce\CollectPlus\Model\OSRef;

/**
 * One page checkout success page
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    const XML_PATH_DEFAULT_MAP_LIST = 'carriers/collect/default_map_list';
    const XML_PATH_COLLECTION_INSTRUCTION = 'carriers/collect/collection_instruction';
    const XML_PATH_COLLECTION_INSTRUCTION2 = 'carriers/collect/collection_instruction2';
    const XML_PATH_GOOGLE_API_KEY = 'carriers/collect/google_map_key';

    /**
     * Helper
     *
     * @var \Jjcommerce\CollectPlus\Helper\Data
     */
    public $_collectHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Jjcommerce\CollectPlus\Helper\Data $_collectHelper,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->_collectHelper = $_collectHelper;
        $order = $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * @param float $GridX
     * @param float $GridY
     * @return null|string
     */
    public function getLatLong($GridX, $GridY)
    {

        $OSRef = new OSRef($GridX, $GridY); //Easting, Northing
        $LatLng = $OSRef->toLatLng();
        $LatLng->toWGS84(); //optional, for GPS compatibility

        $lat =  $LatLng->getLat();
        $long = $LatLng->getLng();
        $latlong = $lat.','.$long;
        return $latlong;
    }

    /**
     * Returns last order id
     *
     * @return null|int
     */
    public function getOrderObject() {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * Returns api key
     *
     * @return null|string
     */
    public function getGoogleApiKey() {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GOOGLE_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return null|string
     */
    public function getCollectionInstruction() {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_COLLECTION_INSTRUCTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return null|string
     */
    public function getCollectionInstruction2() {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_COLLECTION_INSTRUCTION2,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
