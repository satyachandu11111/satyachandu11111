<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Mageplaza\Webhook\Adapter\ShipmentTrack as SpbShipmentTrack;

class ShipmentTrackFactory
{
	public function createFromShipmentTrack(ShipmentTrackInterface $shipmentTrack): SpbShipmentTrack
    {
        $spbShipmentTrack = new SpbShipmentTrack();

//        $spbShipmentTrack->setEntityId($shipmentTrack->getEntityId());
//        $spbShipmentTrack->setOrderId($shipmentTrack->getOrderItemId());
//        $spbShipmentTrack->setShipmentId($shipmentTrack->getShipmentId());
        $spbShipmentTrack->setNumber($shipmentTrack->getTrackNumber());
//        $spbShipmentTrack->setShippingTrackTitle($shipmentTrack->getTitle());
        $spbShipmentTrack->setCourier($shipmentTrack->getCarrierCode());

        return $spbShipmentTrack;
    }
}
