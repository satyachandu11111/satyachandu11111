<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\Metadata\Rule;

use Aheadworks\Raf\Model\Metadata\Rule\Discount\Item;

/**
 * Class Discount
 *
 * @package Aheadworks\Raf\Model\Metadata\Rule
 */
class Discount
{
    /**
     * @var bool
     */
    private $isFriendDiscount;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $percentAmount;

    /**
     * @var string
     */
    private $amountType;

    /**
     * @var float
     */
    private $baseAmount;

    /**
     * @var Item[]
     */
    private $items;

    /**
     * @var float
     */
    private $shippingPercent;

    /**
     * @var float
     */
    private $shippingAmount;

    /**
     * @var float
     */
    private $baseShippingAmount;

    /**
     * Discount constructor
     */
    public function __construct()
    {
        $this
            ->setAmount(0)
            ->setBaseAmount(0)
            ->setShippingAmount(0)
            ->setBaseShippingAmount(0)
            ->setPercentAmount(0)
            ->setItems([]);
    }

    /**
     * Is discount available
     *
     * @return bool
     */
    public function isDiscountAvailable()
    {
        return $this->getAmount() > 0;
    }

    /**
     * Set is friend discount
     *
     * @return bool
     */
    public function isFriendDiscount()
    {
        return $this->isFriendDiscount;
    }

    /**
     * Set is friend discount
     *
     * @param bool $isFriendDiscount
     * @return $this
     */
    public function setIsFriendDiscount($isFriendDiscount)
    {
        $this->isFriendDiscount = $isFriendDiscount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get percent amount
     *
     * @return float
     */
    public function getPercentAmount()
    {
        return $this->percentAmount;
    }

    /**
     * Set percent amount
     *
     * @param float $percentAmount
     * @return $this
     */
    public function setPercentAmount($percentAmount)
    {
        $this->percentAmount = $percentAmount;
        return $this;
    }

    /**
     * Get amount type
     *
     * @return string
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * Set amount type
     *
     * @param string $amountType
     * @return $this
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;
        return $this;
    }

    /**
     * Get base amount
     *
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        $this->baseAmount = $baseAmount;
        return $this;
    }

    /**
     * Get items
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items
     *
     * @param Item[] $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get shipping percent
     *
     * @return float
     */
    public function getShippingPercent()
    {
        return $this->shippingPercent;
    }

    /**
     * Set shipping percent
     *
     * @param float $shippingPercent
     * @return $this
     */
    public function setShippingPercent($shippingPercent)
    {
        $this->shippingPercent = $shippingPercent;
        return $this;
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * Set shipping amount
     *
     * @param float $shippingAmount
     * @return $this
     */
    public function setShippingAmount($shippingAmount)
    {
        $this->shippingAmount = $shippingAmount;
        return $this;
    }

    /**
     * Get base shipping amount
     *
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return $this->baseShippingAmount;
    }

    /**
     * Set base shipping amount
     *
     * @param float $baseShippingAmount
     * @return $this
     */
    public function setBaseShippingAmount($baseShippingAmount)
    {
        $this->baseShippingAmount = $baseShippingAmount;
        return $this;
    }
}
