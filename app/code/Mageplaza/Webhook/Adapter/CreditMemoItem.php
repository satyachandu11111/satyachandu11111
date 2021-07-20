<?php

namespace Mageplaza\Webhook\Adapter;

class CreditMemoItem
{
	/** @var int */
    public $entityId;

    /** @var int */
    public $orderItemId;

    /** @var string */
    public $basePrice;

    /** @var string */
    public $taxAmount;

    /** @var string */
    public $discountAmount;

    /** @var float */
    public $rowTotal = 0;

    /** @var int */
    public $price  = 0;

    /** @var int */
    public $qty  = 0;

    /**
     * @var Adjustments[]
     *
     * @psalm-var Collection<array-key, Adjustments>
     */
    public $adjustments;

    /**
     * Gets the credit memo ID.
     *
     * @return int|null Credit memo ID.
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     */
    public function setEntityId(?int $entityId): void
    {
        $this->entityId = $entityId;
    }

    /**
     * Gets the order item ID.
     *
     * @return int|null order item ID.
     */
    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    /**
     * @param int|null $orderItemId
     */
    public function setOrderItemId(?int $orderItemId): void
    {
        $this->orderItemId = $orderItemId;
    }

    /**
     * Gets the base price for a credit memo item.
     *
     * @return string
     */
    public function getBasePrice(): string
    {
    	return $this->basePrice;
    }

    /**
     *
     * @return string|null
     */
    public function setBasePrice(?string $basePrice): void
    {
    	$this->basePrice = $basePrice;
    }


    /**
     * Gets the Tax Amount for a credit memo item.
     *
     * @return string
     */
    public function getTaxAmount(): string
    {
    	return $this->taxAmount;
    }

    /**
     *
     * @return string|null
     */
    public function setTaxAmount(?string $taxAmount): void
    {
    	$this->taxAmount = $taxAmount;
    }

    /**
     * Gets the discount amount for a credit memo item.
     *
     * @return string|null
     */
    public function getDiscountAmount(): string
    {
    	return $this->discountAmount;
    }

    /**
     *
     * @return string|null
     */
    public function setDiscountAmount(?string $discountAmount): void
    {
    	$this->discountAmount = $discountAmount;
    }


    /**
     * Gets the row total for a credit memo item.
     *
     * @return float
     */
    public function getRowTotal(): float
    {
    	return $this->rowTotal;
    }

    /**
     *
     * @return float|null
     */
    public function setRowTotal(?float $rowTotal): void
    {
    	$this->rowTotal = $rowTotal;
    }


    /**
     * Gets the price for a credit memo item.
     *
     * @return int
     */
    public function getPrice(): int
    {
    	return $this->price;
    }

    /**
     *
     * @return int|null
     */
    public function setPrice(?int $price): void
    {
    	$this->price = $price;
    }

    /**
     * Gets the qty for a credit memo item.
     *
     * @return int
     */
    public function getQty(): int
    {
    	return $this->qty;
    }

    /**
     *
     * @return int|null
     */
    public function setQty(?int $qty): void
    {
    	$this->qty = $qty;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments(): array
    {
        return $this->adjustments;
    }

    /**
     * @param Adjustments[] $adjustments
     */
    public function setAdjustments(array $adjustments): void
    {
        $this->adjustments = $adjustments;
    }

}