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

namespace Jjcommerce\CollectPlus\Block;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Jjcommerce\CollectPlus\Model\OSRef;
use Jjcommerce\CollectPlus\Helper\Data;

class Info extends \Magento\Framework\View\Element\Template
{

    const XML_PATH_DEFAULT_MAP_LIST = 'carriers/collect/default_map_list';
    const XML_PATH_GOOGLE_API_KEY = 'carriers/collect/google_map_key';
    const XML_PATH_COLLECTION_INSTRUCTION = 'carriers/collect/collection_instruction';
    const XML_PATH_COLLECTION_INSTRUCTION2 = 'carriers/collect/collection_instruction2';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * Helper
     *
     * @var \Jjcommerce\CollectPlus\Helper\Data
     */
    public $_collectHelper;

    /**
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        \Jjcommerce\CollectPlus\Helper\Data $_collectHelper,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->paymentHelper = $paymentHelper;
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
        $this->_collectHelper = $_collectHelper;
        parent::__construct($context, $data);
    }




    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrderFromRegistry()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
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
     * @return string
     */
    public function getResponse() {
        $criteria = $this->getRequest()->getParam('criteria');
        return $this->_collectHelper->getPickupList($criteria);
    }

    /**
     * @return null|string
     */
    public function getGoogleApiKey() {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GOOGLE_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return null|int
     */
    public function isModuleEnabled() {
        return $this->_collectHelper->isModuleEnabled();
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
