<?php

namespace Mageplaza\Webhook\Adapter;

class Order
{

    //public $channel = 3;

    /** @var string */
    public $channelOrderId;

    /** @var string|null */
    public $latestShipDate;

    /** @var string|null */
    public $latestDeliveryDate;

    /** @var string */
    public $currencyCode;

    /** @var Address */
    public $shippingAddress;

    /** @var Address */
    public $billingAddress;

    /** @var Collection|BasePaymentInterface[] */
    public $payments = [];

//    /** @var integer|null */
//    public $orderTotalAmount;

    /** @var string|null */
    public $checkoutCompletedAt;

    /** @var string|null */
    public $number;

    /** @var string|null */
    public $notes;

    /** @var string|null */
    public $orderStatus;

    /** @var string|null */
    public $createdAt;

    /** @var string|null */
    public $updatedAt;

    /** @var string|null */
    public $customerEmail;

    /**
     * @var OrderItem[]
     *
     * @psalm-var Collection<array-key, OrderItemInterface>
     */
    public $items;

    /**
     * @var CreditMemo[]
     *
     * @psalm-var Collection<array-key, CreditMemoInterface>
     */
    public $creditMemos;

    /**
     * @var Shipment[]
     *
     * @psalm-var Collection<array-key, ShipmentInterface>
     */
    public $shipments;

    /**
     * @var Adjustment[]
     *
     * @psalm-var Collection<array-key, Adjustments>
     */
    public $adjustments;

//    /** @var int */
//    public $itemsTotal = 0;

//    /** @var int */
//    public $shippingAmount = 0;

    /** @var string */
    public $state = OrderInterface::STATE_CART;

    private $statusArray = [
            'new' => 'payment-pending',
            'processing' => 'processing',
            'complete' => 'shipped',
            'closed' => 'refunded',
            'canceled' => 'canceled'
        ];

    /**
     * @return string
     */
    public function getChannelOrderId(): string
    {
        return $this->channelOrderId;
    }

    /**
     * @param string $channelOrderId
     */
    public function setChannelOrderId(string $channelOrderId): void
    {
        $this->channelOrderId = $channelOrderId;
    }

    /**
     * @return string|null
     */
    public function getLatestShipDate(): ?string
    {
        return $this->latestShipDate;
    }

    /**
     * @param string|null $latestShipDate
     */
    public function setLatestShipDate(?string $latestShipDate): void
    {
        $this->latestShipDate = $latestShipDate;
    }


    /**
     * @return string|null
     */
    public function getLatestDeliveryDate(): ?string
    {
        return $this->latestDeliveryDate;
    }

    /**
     * @param string|null $latestDeliveryDate
     */
    public function setLatestDeliveryDate(?string $latestDeliveryDate): void
    {
        $this->latestDeliveryDate = $latestDeliveryDate;
    }


//    /**
//     * {@inheritdoc}
//     */
//    public function getCustomer(): ?CustomerInterface
//    {
//        return $this->customer;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function setCustomer(?CustomerInterface $customer): void
//    {
//        Assert::nullOrisInstanceOf($customer, CustomerInterface::class);
//
//        $this->customer = $customer;
//    }


    /**
     * {@inheritdoc}
     */
    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(?Address $address): void
    {
        $this->shippingAddress = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(?Address $address): void
    {
        $this->billingAddress = $address;
    }


    /**
     * {@inheritdoc}
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencyCode(?string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }



    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutCompletedAt(): ?string
    {
        return $this->checkoutCompletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutCompletedAt(?string $checkoutCompletedAt): void
    {
        $this->checkoutCompletedAt = $checkoutCompletedAt;
    }



    /**
     * {@inheritdoc}
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param OrderItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }


//    /**
//     * {@inheritdoc}
//     */
//    public function getItemsTotal(): int
//    {
//        return $this->itemsTotal;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function setItemsTotal(): int
//    {
//        $this->itemsTotal = $itemsTotal;
//    }

    /**
     * {@inheritdoc}
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): void
    {
        $this->customerEmail = $customerEmail;
    }


    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?string $orderStatus): void
    {
        $this->orderStatus = isset($this->statusArray[$orderStatus])? $this->statusArray[$orderStatus]: $orderStatus ;
    }

//    /**
//     * @return int|null
//     */
//    public function getShippingAmount(): ?int
//    {
//        return $this->shippingAmount;
//    }
//
//    /**
//     * @param int|null $shippingAmount
//     */
//    public function setShippingAmount(?int $shippingAmount): void
//    {
//        $this->shippingAmount = $shippingAmount;
//    }

    /**
     * {@inheritdoc}
     */
    public function getCreditMemos(): array
    {
        return $this->creditMemos;
    }

    /**
     * @param CreditMemo[] $creditMemos
     */
    public function setCreditMemos(array $creditMemos): void
    {
        $this->creditMemos = $creditMemos;
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments(): array
    {
        return $this->shipments;
    }

    /**
     * @param Shipment[] $shipments
     */
    public function setShipments(array $shipments): void
    {
        $this->shipments = $shipments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments(): array
    {
        return $this->adjustments;
    }

    /**
     * @param Adjustment[] $adjustments
     */
    public function setAdjustments(array $adjustments): void
    {
        $this->adjustments = $adjustments;
    }

}
