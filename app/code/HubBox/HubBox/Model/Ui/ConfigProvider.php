<?php

namespace HubBox\HubBox\Model\Ui;

use HubBox\HubBox\Helper\Data;
use HubBox\HubBox\Model\CollectableInterface;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    protected $_helper;
    protected $_collectable;
    protected $_storeManager;

    /**
     * ConfigProvider constructor.
     * @param Data $helper
     * @param CollectableInterface $collectable
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helper,
        CollectableInterface $collectable,
        StoreManagerInterface $storeManager
    ) {
        $this->_helper = $helper;
        $this->_collectable = $collectable;
        $this->_storeManager = $storeManager;
    }

    /**
    * Retrieve assoc array of checkout configuration
    *
    * @return array
    */
    public function getConfig()
    {
        $locationsFeed = $this->getLocationFeedUrl(
            $this->_helper->getEnvironment(),
            $this->_helper->isEnabled() && $this->_collectable->isCollectable(),
            $this->_helper->getPrivateSlug()

        );
        return [
            'hubBox' => [
                'isClickAndCollectable' => $this->_helper->isEnabled() && $this->_collectable->isCollectable(),
                'googleMapsKey'         => $this->_helper->getGoogleMapsKey(),
                'searchUrlNearest'      => $locationsFeed . '/nearest',
                'searchUrlWithin'       => $locationsFeed . '/within',
                'privatePickupMessage'  => $this->_helper->getPrivatePickupMessage(),
                'privatePinUrl'         => $this->_helper->getPrivatePinUrl(),
                'showFirstLastname'     => $this->_helper->showFirstlastname(),
                'privateBoost'          => $this->_helper->getBoost(),
                'privateDistance'       => $this->_helper->getPrivateDistance()
                ]
        ];
    }

    private function getHubBoxBaseUrl($environment)
    {
        if ($environment === 'production') {
            return 'https://api.hub-box.com/v1';
        }
        return 'https://sandbox.api.hub-box.com/v1';
    }

    private function getLocationFeedUrl($environment, $private, $privateSlug)
    {
        $base = $this->getHubBoxBaseUrl($environment);
        if ($private === true && $privateSlug) {
            return $base . '/public/private-network/' . $privateSlug;
        } else {
            return $base . '/public/collectpoints';
        }
    }
}
