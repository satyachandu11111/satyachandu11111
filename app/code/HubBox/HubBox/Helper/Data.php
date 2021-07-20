<?php
/**
 * HubBox Click and Collect
 * Copyright (C) 2017  2017
 *
 * This file is part of HubBox/HubBox.
 *
 * HubBox/HubBox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace HubBox\HubBox\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper {

    protected $storeManager;
    protected $objectManager;

    const XML_PATH_HUBBOX = 'hubbox/';

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager)
    {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function isEnabled() {
        return (bool) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'general/enable', ScopeInterface::SCOPE_STORE);
    }

    public function getEnvironment() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'general/environment', ScopeInterface::SCOPE_STORE);
    }

    public function getGoogleMapsKey() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'google/google_maps_key', ScopeInterface::SCOPE_STORE);
    }

    public function hasPrivateCollectPoints() {
        return (bool) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/private', ScopeInterface::SCOPE_STORE);
    }

    public function getPrivateSlug() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/private_slug', ScopeInterface::SCOPE_STORE);
    }

    public function getPrivatePickupMessage() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/private_message', ScopeInterface::SCOPE_STORE);
    }

    public function getPrivatePinUrl() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/private_pin', ScopeInterface::SCOPE_STORE);
    }

    public function showFirstlastname() {
        return (bool) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/show_firstlastname', ScopeInterface::SCOPE_STORE);
    }

    public function getNumberOfCollectPoints() {
        return (int) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/limit', ScopeInterface::SCOPE_STORE);
    }

    public function getDistanceLimit() {
        return (int) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'ui/distance', ScopeInterface::SCOPE_STORE);
    }

    public function isFree() {
        return (bool) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'pricing/free', ScopeInterface::SCOPE_STORE);
    }

    public function addLowerFee() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'pricing/less', ScopeInterface::SCOPE_STORE);
    }

    public function addHigherFee() {
        return $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'pricing/more', ScopeInterface::SCOPE_STORE);
    }

    public function getBasketCutOff() {
        return (int) $this->scopeConfig->getValue( self::XML_PATH_HUBBOX . 'pricing/cutoff', ScopeInterface::SCOPE_STORE);
    }

    public function getHubBoxApiUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_HUBBOX . 'general/environment', ScopeInterface::SCOPE_STORE);
    }

    public function getHubBoxApiUsername()
    {
        return trim($this->scopeConfig->getValue(self::XML_PATH_HUBBOX . 'api/username', ScopeInterface::SCOPE_STORE));
    }

    public function getHubBoxApiKey()
    {
        return trim($this->scopeConfig->getValue(self::XML_PATH_HUBBOX . 'api/api_key', ScopeInterface::SCOPE_STORE));
    }

    public function getLabelAppend()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_HUBBOX . 'label/append', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool should private location be boosted to top of list
     *
     */
    public function getBoost()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_HUBBOX. 'ui/private_boost', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed distance in KM to search for private store
     */
    public function getPrivateDistance() {
        return $this->scopeConfig->getValue(self::XML_PATH_HUBBOX . 'ui/private_distance', ScopeInterface::SCOPE_STORE);
    }


}
