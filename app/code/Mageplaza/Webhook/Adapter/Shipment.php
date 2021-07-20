<?php

namespace Mageplaza\Webhook\Adapter;

class Shipment
{
    /** @var string  */
    public $method = '1';

    /** @var string  */
    public $shipmentType = 'shipment';

	/** @var int */
    public $entityId;

    /** @var string */
    public $order;

//    /** @var float */
//    public $qty;

    /** @var string|null */
    public $createdAt;

    /** @var string|null */
    public $updatedAt;

    /**
     * @var ShipmentItem[]
     *
     * @psalm-var Collection<array-key, ShipmentItemInterface>
     */
    public $units;

    /**
     * @var ShipmentTrack
     *
     */
    public $courierTracking;

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     */
    public function setEntityId(int $entityId): void
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @param string|null $createdAt
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @param string|null $updatedAt
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return ShipmentItem[]
     */
    public function getUnits(): array
    {
        return $this->units;
    }

    /**
     * @param ShipmentItem[] $units
     */
    public function setUnits(array $units): void
    {
        $this->units = $units;
    }

    /**
     * @return ShipmentTrack
     */
    public function getCourierTracking(): ShipmentTrack
    {
        return $this->courierTracking;
    }

    /**
     * @param ShipmentTrack $courierTracking
     */
    public function setCourierTracking(ShipmentTrack $courierTracking): void
    {
        $this->courierTracking = $courierTracking;
    }


    /**
     * @return shipmentType
     */
    public function getShipmentType(): ?string
    {
        return $this->shipmentType;
    }



}
