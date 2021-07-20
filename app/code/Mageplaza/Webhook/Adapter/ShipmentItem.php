<?php

namespace Mageplaza\Webhook\Adapter;


class ShipmentItem
{
//	/** @var int */
//    public $entityId;

//    /** @var int */
//    public $shipmentId;

    /** @var int */
    public $quantity;

    /** @var string */
    public $orderItem;

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getOrderItem(): string
    {
        return $this->orderItem;
    }

    /**
     * @param string $orderItem
     */
    public function setOrderItem(string $orderItem): void
    {
        $this->orderItem = $orderItem;
    }
}
