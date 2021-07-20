<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\ShipmentItemInterface;
use Mageplaza\Webhook\Adapter\ShipmentItem as SpbShipmentItem;

class ShipmentItemFactory
{
	public function createFromShipmentItem(ShipmentItemInterface $shipmentItem): SpbShipmentItem
    {
        $spbShipmentItem = new SpbShipmentItem();

//        $spbShipmentItem->setEntityId($shipmentItem->getEntityId());
        $spbShipmentItem->setOrderItem((string) $shipmentItem->getOrderItemId());
//        $spbShipmentItem->setShipmentId($shipmentItem->getShipmentId());
        $spbShipmentItem->setQuantity($shipmentItem->getQty());

        return $spbShipmentItem;
    }
}
