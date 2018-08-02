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

use Jjcommerce\CollectPlus\Model\OSRef;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template;
use Jjcommerce\CollectPlus\Helper\Data;

class Collect extends \Magento\Framework\View\Element\Template
{

    const XML_PATH_DEFAULT_MAP_LIST = 'carriers/collect/default_map_list';
    const XML_PATH_GOOGLE_API_KEY = 'carriers/collect/google_map_key';
    const XML_PATH_COLLECTION_INSTRUCTION = 'carriers/collect/collection_instruction';
    const XML_PATH_COLLECTION_INSTRUCTION2 = 'carriers/collect/collection_instruction2';

    /**
     * Helper
     *
     * @var \Jjcommerce\CollectPlus\Helper\Data
     */
    public $_collectHelper;

    /**
     * @param TemplateContext $_collectHelper
     * @param TemplateContext $context
     */
    public function __construct(
        \Jjcommerce\CollectPlus\Helper\Data $_collectHelper,
        \Magento\Framework\View\Element\Template\Context $context
        )
    {
        $this->_collectHelper = $_collectHelper;
        parent::__construct($context);
    }

    /**
     * @return null|int
     */
    public function canUseCurrentLocation() {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_DEFAULT_MAP_LIST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
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
     * @param string $postcode
     * @return false|string
     */
    public function getDefaultLocation($postcode)
    {
        if ($postcode) {
            $address = urlencode($postcode . ',UK');
            $googleApiKey = $this->getGoogleApiKey();
            $url = "https://maps.googleapis.com/maps/api/geocode/json?key=$googleApiKey&address=$address&sensor=false";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            try {
                $result = curl_exec($ch);
                curl_close($ch);
            } catch (Exception $e) {
                //echo $e->getMessage();
            }
            $json = json_decode($result);

            $lat[0] = $json->results[0]->geometry->location->lat;
            $lng[0] = $json->results[0]->geometry->location->lng;

            if ($lat[0]) {
                return "{$lat[0]},{$lng[0]}";
            }
        }
        return false;
    }

    public function getResponse() {
        $criteria = $this->getRequest()->getParam('criteria');
        return $this->_collectHelper->getPickupList(preg_replace('/\s+/', '', $criteria));
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
     * @return null|int
     */
    public function isShowCollect() {
        return $this->_collectHelper->isShowCollect();
    }

    /**
     * @return null|int
     */
    public function canShowSmsBox() {
        return $this->_collectHelper->canShowSmsBox();
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
