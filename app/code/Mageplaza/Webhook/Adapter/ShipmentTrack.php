<?php

namespace Mageplaza\Webhook\Adapter;

class ShipmentTrack
{
//	/** @var int */
//    public $entityId;

//    /** @var int */
//    public $shipmentId;

//    /** @var int */
//    public $orderId;

    /** @var string */
    public $number;

//    /** @var string */
//    public $title;

    /** @var string */
    public $courier;

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getCourier(): string
    {
        return $this->courier;
    }

    /**
     * @param string $courier
     */
    public function setCourier(string $courier): void
    {
        $this->courier = $courier;
    }


}
