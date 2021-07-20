<?php

namespace Mageplaza\Webhook\Adapter;

class CreditMemo
{

	/** @var int */
    public $entityId;

    /** @var int */
    public $orderId;

    /** @var float */
    public $subtotal;

    /**
     * Returns state
     *
     * STATE_OPEN = 1,STATE_REFUNDED = 2, STATE_CANCELED = 3
     *
     * @return int
     */
    public $state;

    /**
     * @var CreditMemoItem[]
     *
     * @psalm-var Collection<array-key, CreditmemoItemInterface>
     */
    public $items;

    /**
     * @var CreditMemoComment[]
     *
     * @psalm-var Collection<array-key, CreditmemoCommentInterface>
     */
    public $comments;

    /** @var string */
    public $adjustment;

    /** @var string|null */
    public $createdAt;

    /** @var string|null */
    public $updatedAt;

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
     * Gets the order ID.
     *
     * @return int|null Order ID.
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int|null $orderId
     */
    public function setOrderId(?int $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * Gets Sub Total.
     *
     * @return float|null Sub Total.
     */
    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    /**
     * @param float|null $subtotal
     */
    public function setSubtotal(?float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    /* Gets State.
     *
     * STATE_OPEN = 1,STATE_REFUNDED = 2, STATE_CANCELED = 3
     *
     * @return int|null State.
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * STATE_OPEN = 1,STATE_REFUNDED = 2, STATE_CANCELED = 3
     *
     * @param int|null $state
     */
    public function setState(?int $state): void
    {
        $this->state = $state;
    }


    /**
     * {@inheritdoc}
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param CreditMemoItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param CreditMemoComment[] $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }

    /* Gets Shipping Amount.
     *
     * @return string|null Shipping Amount.
     */
    public function getShippingAmount(): string
    {
        return $this->shippingAmount;
    }

    /**
     * @param string|null $shippingAmount
     */
    public function setShippingAmount(?string $shippingAmount): void
    {
        $this->shippingAmount = $shippingAmount;
    }

    /* Gets Adjustment.
     *
     * @return string|null Adjustment.
     */
    public function getAdjustment(): string
    {
        return $this->adjustment;
    }

    /**
     * @param string|null $adjustment
     */
    public function setAdjustment(?string $adjustment): void
    {
        $this->adjustment = $adjustment;
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