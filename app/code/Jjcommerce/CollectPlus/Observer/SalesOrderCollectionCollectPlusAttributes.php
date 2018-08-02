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

namespace Jjcommerce\CollectPlus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;

class SalesOrderCollectionCollectPlusAttributes implements ObserverInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */


    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * add delivery comment to order object
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orders = $observer->getOrderCollection();

        $next_day_account = $this->getConfigValue('carriers/collect/next_day_account');
        $two_day_account = $this->getConfigValue('carriers/collect/two_day_account');
        $three_day_account = $this->getConfigValue('carriers/collect/three_day_account');

        foreach($orders as $order) {
            if(!$order) {
                continue;
            }
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes === null) {
                //$extensionAttributes = $this->getOrderExtensionDependency();
                $extensionAttributes = $this->getOrderExtensionFactory()->create();
            }

            $shippingMethod = $order->getShippingMethod();
            $collectPlusAccountNumber = '';
            $agentData = unserialize($order->getAgentData());
            if (strpos($shippingMethod, 'next')) {
                $collectPlusAccountNumber = $next_day_account;
            } elseif(strpos($shippingMethod, 'two')) {
                $collectPlusAccountNumber = $two_day_account;
            } elseif(strpos($shippingMethod, 'three')) {
                $collectPlusAccountNumber = $three_day_account;
            }

            $extensionAttributes->setData('collectplus_account_code', $collectPlusAccountNumber ? $collectPlusAccountNumber : '');
            $extensionAttributes->setData('collectplus_sms_alert', $order->getSmsAlert() ? $order->getSmsAlert() : '');
            $extensionAttributes->setData('collectplus_site_number', isset($agentData['SiteNumber']) ? $agentData['SiteNumber'] : '' );
            $extensionAttributes->setData('collectplus_address1', isset($agentData['SiteName']) ? $agentData['SiteName'] : '' );
            $extensionAttributes->setData('collectplus_address2', isset($agentData['Address']) ? $agentData['Address'] : '' );
            $extensionAttributes->setData('collectplus_address3', isset($agentData['City']) ? $agentData['City'] : '' );
            $extensionAttributes->setData('collectplus_address4', isset($agentData['County']) ? $agentData['County'] : '' );
            $extensionAttributes->setData('collectplus_postcode', isset($agentData['Postcode']) ? $agentData['Postcode'] : '' );
            $extensionAttributes->setData('collectplus_HDN', isset($agentData['HDNCode']) ? $agentData['HDNCode'] : '' );

            $order->setExtensionAttributes($extensionAttributes);

        }
    }

    /**
     * Get the new OrderExtensionFactory for application code
     *
     * @return OrderExtensionFactory
     */
    private function getOrderExtensionFactory()
    {
        if (!$this->orderExtensionFactory instanceof OrderExtensionFactory) {
            $this->orderExtensionFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Sales\Api\Data\OrderExtensionFactory'
            );
        }
        return $this->orderExtensionFactory;
    }

    /**
     * Get the new OrderExtension for application code
     *
     * @return \Magento\Sales\Api\Data\OrderExtension
     */
    private function getOrderExtensionDependency()
    {
        $orderExtension = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Sales\Api\Data\OrderExtension');

        return $orderExtension;
    }

    public function getConfigValue($path)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
