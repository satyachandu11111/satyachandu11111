<?php


namespace Mageplaza\Webhook\Adapter;


class OrderItem
{
    /** @var string */
    public $channelItemId;

    /** @var string|null */
    public $sku;

    /** @var string */
    public $channelProductId;

    /** @var string|null */
    public $title;

    /** @var string|null */
    public $conditionId;

    /** @var string|null */
    public $conditionSubTypeId;

    /** @var string|null */
    public $conditionNote;

//    /** @var integer|null */
//    public $itemAmount;

    /** @var string|null  */
    public $imageUrl;


    /** @var int */
    public $quantity = 0;

    /** @var int */
    public $unitPrice = 0;

//    /** @var int */
//    public $total = 0;

//    /** @var int */
//    public $taxAmount = 0;

//    /** @var int */
//    public $discountAmount = 0;

    /**
     * @var Adjustment[]
     *
     * @psalm-var Collection<array-key, Adjustments>
     */
    public $adjustments;

    /**
     * @return string
     */
    public function getChannelItemId(): string
    {
        return $this->channelItemId;
    }

    /**
     * @param string $channelItemId
     */
    public function setChannelItemId(string $channelItemId): void
    {
        $this->channelItemId = $channelItemId;
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @param string|null $sku
     */
    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @return string|null
     */
    public function getChannelProductId(): ?string
    {
        return $this->channelProductId;
    }

    /**
     * @param string|null $channelProductId
     */
    public function setChannelProductId(?string $channelProductId): void
    {
        $this->channelProductId = $channelProductId;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getConditionId(): ?string
    {
        return $this->conditionId;
    }

    /**
     * @param string|null $conditionId
     */
    public function setConditionId(?string $conditionId): void
    {
        $this->conditionId = $conditionId;
    }

    /**
     * @return string|null
     */
    public function getConditionSubTypeId(): ?string
    {
        return $this->conditionSubTypeId;
    }

    /**
     * @param string|null $conditionSubTypeId
     */
    public function setConditionSubTypeId(?string $conditionSubTypeId): void
    {
        $this->conditionSubTypeId = $conditionSubTypeId;
    }

    /**
     * @return string|null
     */
    public function getConditionNote(): ?string
    {
        return $this->conditionNote;
    }

    /**
     * @param string|null $conditionNote
     */
    public function setConditionNote(?string $conditionNote): void
    {
        $this->conditionNote = $conditionNote;
    }


    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $imageUrl
     */
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

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
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

//    /**
//     * @return int
//     */
//    public function getTotal(): int
//    {
//        return $this->total;
//    }
//
//    /**
//     * @param int $total
//     */
//    public function setTotal(int $total): void
//    {
//        $this->total = $total;
//    }

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
