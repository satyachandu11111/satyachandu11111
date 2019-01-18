<?php
namespace Dividebuy\CheckoutConfig\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class DeleteDbShipment implements ObserverInterface
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
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderLoader;

    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $_shipmentLoader;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\RetailerConfiguration
     */
    protected $_retailerConfigurationHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Dividebuy\RetailerConfig\Helper\Data   $retailerHelper
     * @param \Dividebuy\CheckoutConfig\Helper\Api    $apiHelper
     * @param \Magento\Framework\Json\Helper\Data     $jsonHelper
     * @param \Psr\Log\LoggerInterface                $logger
     * @param \Magento\Sales\Model\Order              $order
     * @param \Magento\Sales\Model\Order\Shipment     $shipment
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Dividebuy\RetailerConfig\Helper\Data $retailerHelper,
        \Dividebuy\CheckoutConfig\Helper\Api $apiHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Dividebuy\RetailerConfig\Helper\RetailerConfiguration $retailerConfigurationHelper
    ) {
        $this->_request                     = $request;
        $this->_retailerHelper              = $retailerHelper;
        $this->_apiHelper                   = $apiHelper;
        $this->_jsonHelper                  = $jsonHelper;
        $this->_logger                      = $logger;
        $this->_orderLoader                 = $order;
        $this->_shipmentLoader              = $shipment;
        $this->_retailerConfigurationHelper = $retailerConfigurationHelper;
    }

    /**
     * Check if the order payment method is DivideBuy
     *
     * @param  object $order
     * @return boolean
     */
    protected function _checkDivideBuyCarrier($order)
    {
        $flag          = false;
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
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $tracking    = $observer->getEvent()->getTrack();
        $orderId     = $tracking->getOrderId();
        $order       = $this->_orderLoader->load($orderId);
        $isDivideBuy = $this->_checkDivideBuyCarrier($order);

        if ($isDivideBuy) {
            $shipment       = $this->_shipmentLoader->load($tracking->getParentId());
            $deleteTracking = 1;
            $params         = $this->_getTrackingRequest($shipment, $deleteTracking, $tracking);
            $params         = $this->_jsonHelper->jsonEncode($params);
            $url            = $this->_retailerConfigurationHelper->getApiUrl($this->_retailerHelper->getStoreId()) . 'api/tracking';
            $response       = $this->_apiHelper->sendRequest($url, $params);

            if (empty($response['status']) && !empty($response['error'])) {
                throw new LocalizedException(__('Unable to delete shipment in DivideBuy: ' . $response['message'][0]));
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
