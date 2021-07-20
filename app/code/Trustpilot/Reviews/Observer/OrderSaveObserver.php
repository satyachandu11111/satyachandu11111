<?php
namespace Trustpilot\Reviews\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Psr\Log\LoggerInterface;
use Trustpilot\Reviews\Helper\OrderData;
use Trustpilot\Reviews\Helper\Data;
use Trustpilot\Reviews\Helper\TrustpilotHttpClient;
use Magento\Config\Model\ResourceModel\Config;

define('__ACCEPTED__', 202);

class OrderSaveObserver implements ObserverInterface
{   

    protected $_trustpilotHttpClient;
    protected $_logger;
    protected $_orderData;
    protected $_helper;
    protected $_config;
    
    public function __construct(
        LoggerInterface $logger, 
        TrustpilotHttpClient $trustpilotHttpClient,
        OrderData $orderData,
        Data $helper,
        Config $config)
    {
        $this->_helper = $helper;
        $this->_trustpilotHttpClient = $trustpilotHttpClient;
        $this->_logger = $logger; 
        $this->_orderData = $orderData;
        $this->_config = $config;
    }
  
    public function execute(EventObserver $observer) 
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $orderStatus = $order->getState();
        $storeId = $order->getStoreId();

        $settings = json_decode($this->_helper->getConfig('master_settings_field', $storeId));
        $key = $settings->general->key;

        try {
            if (isset($key)) {
                $data = $this->_orderData->getInvitation($order, 'sales_order_save_after', \Trustpilot\Reviews\Model\Config::WITHOUT_PRODUCT_DATA);

                if (in_array($orderStatus, $settings->general->mappedInvitationTrigger)) {
                    $response = $this->_trustpilotHttpClient->postInvitation($key, $storeId, $data);

                    if ($response['code'] == __ACCEPTED__) {
                        $data = $this->_orderData->getInvitation($order, 'sales_order_save_after', \Trustpilot\Reviews\Model\Config::WITH_PRODUCT_DATA);
                        $response = $this->_trustpilotHttpClient->postInvitation($key, $storeId, $data);
                    }
                    $this->handleSingleResponse($response, $data, $storeId);
                } else {
                    $data['payloadType'] = 'OrderStatusUpdate';
                    $this->_trustpilotHttpClient->postInvitation($key, $storeId, $data);
                }
                return;
            }
        } catch (\Exception $e) {
            $error = array('message' => $e->getMessage());
            $data = array('error' => $error);
            $this->_trustpilotHttpClient->postInvitation($key, $storeId, $data);
            return;
        }
    }

    public function handleSingleResponse($response, $order, $storeId)
    {
        try {
            $scope = $this->_helper->getScope();
            $synced_orders = (int) $this->_helper->getConfig('past_orders', $storeId);
            $failed_orders = json_decode($this->_helper->getConfig('failed_orders', $storeId));

            if ($response['code'] == 201) {
                $synced_orders = (int) ($synced_orders + 1);
                $this->saveConfig('past_orders', $synced_orders, $scope, $storeId);
                if (isset($failed_orders->{$order['referenceId']})) {
                    unset($failed_orders->{$order['referenceId']});
                    $this->saveConfig('failed_orders', json_encode($failed_orders), $scope, $storeId);
                }
            } else {
                $failed_orders->{$order['referenceId']} = base64_encode('Automatic invitation sending failed');
                $this->saveConfig('failed_orders', json_encode($failed_orders), $scope, $storeId);
            }
        } catch (\Exception $e) {
            $message = 'Unable to update past orders. Error: ' . $e->getMessage();
        }
    }

    private function saveConfig($config, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $path = 'trustpilot/trustpilot_general_group/';

        if ($scope === 'store') {
            $scope = 'stores';
        } elseif ($scope === 'website') {
            $scope = 'websites';
        }

        $this->_config->saveConfig($path . $config,  $value, $scope, $scopeId);
    }
}
