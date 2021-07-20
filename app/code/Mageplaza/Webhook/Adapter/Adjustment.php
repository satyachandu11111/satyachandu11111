<?php

namespace Mageplaza\Webhook\Adapter;

class Adjustment
{
    /*
     * Shipping Adjustment.
     */
    const SHIPPING = "shipping";

    /*
     * DISCOUNT Adjustment.
     */
    const DISCOUNT = "discount";

    /*
     * Tax Adjustment.
     */
    const TAX = "tax";

    /*
     * GIFT WRAPPER Adjustment.
     */
    const GIFT_WRAPPER = "gift_wrapper";

    /*
     * GIFT WRAPPER TAX Adjustment.
     */
    const GIFT_WRAPPER_TAX = "gift_wrapper_tax";

    /*
     * WEEE TAX Adjustment.
     */
    const WEEE_TAX = "weee_tax";

    /*
     * GIFT WRAPPER REFUNDED Adjustment.
     */
    const GIFT_WRAPPER_REFUNDED = "gift_wrapper_refunded";

    /*
     * Tax REFUNDED Adjustment.
     */
    const Tax_REFUNDED = "tax_refunded";

    /*
     * DISCOUNT_REFUNDED Adjustment.
     */
    const DISCOUNT_REFUNDED = "discount_refunded";

    /*
     * SHIPPING_REFUNDED Adjustment.
     */
    const SHIPPING_REFUNDED = "shipping_refunded";

    /*
     * ADJUSTMENTPOSITIVE Adjustment.
     */
    const ADJUSTMENTPOSITIVE = "adjustment_positive";

    /*
     * ADJUSTMENTNEGATIVE Adjustment.
     */
    const ADJUSTMENTNEGATIVE = "adjustment_negative";

	/** @var int */
    public $orderId;

    /** @var int */
    public $orderItemId;

    /** @var string|null */
    public $type;

    /** @var string|null */
    public $label;

    /** @var string|null */
    public $originCode;

    /** @var int|null */
    public $amount;

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
     * Gets the order item ID.
     *
     * @return int|null Order Item ID.
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

    /* Gets type.
     *
     * @return string|null type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /* Gets label.
     *
     * @return string|null label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /* Gets origin Code.
     *
     * @return string|null origin Code.
     */
    public function getOriginCode(): string
    {
        return $this->originCode;
    }

    /**
     * @param string|null $originCode
     */
    public function setOriginCode(?string $originCode): void
    {
        $this->originCode = $originCode;
    }

    /* Gets amount.
     *
     * @return string|null amount.
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param string|null $amount
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount*100;
    }
}
