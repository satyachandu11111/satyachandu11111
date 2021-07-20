<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use Mageplaza\Webhook\Adapter\Shipment as SpbShipment;

class ShipmentFactory
{
	public function __construct(ShipmentItemFactory $shipmentItemFactory, ShipmentTrackFactory $shipmentTrackFactory)
    {
        $this->shipmentItemFactory = $shipmentItemFactory;
        $this->shipmentTrackFactory = $shipmentTrackFactory;
    }

    /**
     * @param  $shipment
     */
    public function createFromShipment(ShipmentInterface $shipment): SpbShipment
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

//        $logger->info('started');

        $spbShipment = new SpbShipment();
//        $logger->info('before order');

//        $spbShipment->setEntityId($shipment->getEntityId());
        $spbShipment->setOrder((string) $shipment->getOrderId());
        $spbShipment->setCreatedAt($shipment->getCreatedAt());
        $spbShipment->setUpdatedAt($shipment->getUpdatedAt());
//        $spbShipment->setTotalQty($shipment->getTotalQty());
        $items = [];
//        $logger->info('items');

        foreach ($shipment->getItems() as $shipmentItem){
            $items []= $this->shipmentItemFactory->createFromShipmentItem($shipmentItem);
        }

        $tracks = [];
//        $logger->info('tracks');

        foreach ($shipment->getTracks() as $shipmentTrack){
            $tracks []= $this->shipmentTrackFactory->createFromShipmentTrack($shipmentTrack);
        }

        $spbShipment->setUnits($items);
        if(isset($tracks[0])) {
            $spbShipment->setCourierTracking($tracks[0]);
        }


        $logger->info(print_r($spbShipment,1));

        return $spbShipment;
    }
}
