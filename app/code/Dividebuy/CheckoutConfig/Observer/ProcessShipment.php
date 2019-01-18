<?php
namespace Dividebuy\CheckoutConfig\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class ProcessShipment implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_retailerHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Dividebuy\CheckoutConfig\Helper\Api
     */
    protected $_apiHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface     $request
     * @param \Dividebuy\RetailerConfig\Helper\Data       $retailerHelper
     * @param \Dividebuy\CheckoutConfig\Helper\Api        $apiHelper
     * @param \Magento\Framework\Json\Helper\Data         $jsonHelper
     * @param \Psr\Log\LoggerInterface                    $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Dividebuy\RetailerConfig\Helper\Data $retailerHelper,
        \Dividebuy\CheckoutConfig\Helper\Api $apiHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper
    ) {
        $this->_request                     = $request;
        $this->_retailerHelper              = $retailerHelper;
        $this->_apiHelper                   = $apiHelper;
        $this->_jsonHelper                  = $jsonHelper;
        $this->_logger                      = $logger;
        $this->_messageManager              = $messageManager;
        $this->_customerSession             = $customerSession;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
    }

    /**
     * Check if the order payment method is DivideBuy
     *
     * @param  object $order
     * @return boolean
     */
    protected function _checkDivideBuyCarrier($shipment)
    {
        $flag          = false;
        $order         = $shipment->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();
        if ($paymentMethod == \Dividebuy\Payment\Helper\Data::DIVIDEBUY_PAYMENT_CODE) {
            $flag = true;
        }
        return $flag;
    }

    /**
     * Used to get shipment tracking details
     *
     * @return array
     */
    private function getShipmentTracking()
    {
        $shipmentTrack = $this->_request->getParam('tracking');
        if (isset($shipmentTrack) && !empty($shipmentTrack)) {
            $shipmentTrackData = array();
            $i                 = 0;
            foreach ($shipmentTrack as $track) {
                $shipmentTrackData[$i]['trackNumber'] = $track['number'];
                $shipmentTrackData[$i]['description'] = '';
                $shipmentTrackData[$i]['title']       = $track['title'];
                $shipmentTrackData[$i]['carrierCode'] = $track['carrier_code'];
                $i++;
            }
            return $shipmentTrackData;
        }
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment      = $observer->getEvent()->getShipment();
        $isDivideBuy   = $this->_checkDivideBuyCarrier($shipment);
        $shipmentTrack = $this->_request->getParam('tracking');
        if ($isDivideBuy && (isset($shipmentTrack) && !empty($shipmentTrack))) {
            $this->_customerSession->setIsNewShipment(1);
            $params   = $this->_getTrackingRequest($shipment);
            $params   = $this->_jsonHelper->jsonEncode($params);
            $url      = $this->_retailerConfigurationHelper->getApiUrl($this->_retailerHelper->getStoreId()) . 'api/tracking';
            $response = $this->_apiHelper->sendRequest($url, $params);
            //echo '<pre>';print_r($response);die('93'.__FILE__);
            if (empty($response['status']) && !empty($response['error'])) {
                throw new LocalizedException(__('Unable to save shipment in DivideBuy: ' . $response['message'][0]));
            }
        }
    }

    /**
     * Will create a tracking request for DivideBuy
     *
     * @param  object  $shipment
     * @param  integer $deleteTracking
     * @param  array $tracking
     * @return array
     */
    protected function _getTrackingRequest($shipment, $deleteTracking = 0, $tracking = null)
    {
        $params = array();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();

        $storeId = $order->getStoreId();
        //$helper = Mage::helper('retailer_config');

        $params['retailerId']          = $this->_retailerHelper->getRetailerId($storeId); //Mage::getStoreConfig(Dividebuy_RetailerConfig_Helper_Data::XML_PATH_RETAILER_ID, $storeId);
        $params['storeOrderId']        = $shipment->getOrderId();
        $params['storeToken']          = $this->_retailerHelper->getTokenNumber($storeId);
        $params['storeAuthentication'] = $this->_retailerHelper->getAuthNumber($storeId);
        $params['deleteTracking']      = $deleteTracking;

        if (!$deleteTracking) {
            $params['trackingInfo'] = $this->getShipmentTracking($shipment->getOrderId(), $shipment->getId());
        } else {
            $trackArray                = array();
            $trackArray['trackNumber'] = $tracking->getTrackNumber();
            $trackArray['title']       = $tracking->getTitle();
            $trackArray['carrierCode'] = $tracking->getCarrierCode();
            $params['trackingInfo'][]  = $trackArray;
        }

        //getting product Information
        $i = 0;
        foreach ($shipment->getAllItems() as $item) {
            $productDetails[$i]['sku'] = $item->getSku();
            $productDetails[$i]['qty'] = $item->getQty();
            $i++;
        }
        $params['productDetails'] = $productDetails;

        return $params;
    }
}
