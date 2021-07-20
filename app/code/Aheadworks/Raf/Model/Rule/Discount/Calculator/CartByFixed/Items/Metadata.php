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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator\CartByFixed\Items;

/**
 * Class Metadata
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\CartByFixed\Items
 */
class Metadata
{
    /**
     * @var float
     */
    private $availableAmount;

    /**
     * @var float
     */
    private $baseAvailableAmount;

    /**
     * @var float
     */
    private $baseItemsTotal;

    /**
     * @var float
     */
    private $itemsTotal;

    /**
     * @var float
     */
    private $baseUsedAmount;

    /**
     * @var float
     */
    private $usedAmount;

    /**
     * @var int
     */
    private $itemsCount;

    /**
     * Metadata constructor
     */
    public function __construct()
    {
        $this
            ->setAvailableAmount(0)
            ->setBaseAvailableAmount(0)
            ->setItemsTotal(0)
            ->setBaseItemsTotal(0)
            ->setUsedAmount(0)
            ->setBaseUsedAmount(0)
            ->setItemsCount(0);
    }

    /**
     * Get base available amount left
     *
     * @return float
     */
    public function getBaseAvailableAmountLeft()
    {
        return max(0, $this->baseAvailableAmount - $this->baseUsedAmount);
    }

    /**
     * Get available amount left
     *
     * @return float
     */
    public function getAvailableAmountLeft()
    {
        return max(0, $this->availableAmount - $this->usedAmount);
    }

    /**
     * Get available amount
     *
     * @return float
     */
    public function getAvailableAmount()
    {
        return $this->availableAmount;
    }

    /**
     * Set available amount
     *
     * @param float $availableAmount
     * @return $this
     */
    public function setAvailableAmount($availableAmount)
    {
        $this->availableAmount = $availableAmount;
        return $this;
    }

    /**
     * Set base available amount
     *
     * @return float
     */
    public function getBaseAvailableAmount()
    {
        return $this->baseAvailableAmount;
    }

    /**
     * Get base available amount
     *
     * @param float $baseAvailableAmount
     * @return $this
     */
    public function setBaseAvailableAmount($baseAvailableAmount)
    {
        $this->baseAvailableAmount = $baseAvailableAmount;
        return $this;
    }

    /**
     * Get base items total
     *
     * @return float
     */
    public function getBaseItemsTotal()
    {
        return $this->baseItemsTotal;
    }

    /**
     * Set base items total
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseItemsTotal($amount)
    {
        $this->baseItemsTotal = $amount;
        return $this;
    }

    /**
     * Get used items total
     *
     * @return float
     */
    public function getItemsTotal()
    {
        return $this->itemsTotal;
    }

    /**
     * Set used items total
     *
     * @param float $amount
     * @return $this
     */
    public function setItemsTotal($amount)
    {
        $this->itemsTotal = $amount;
        return $this;
    }

    /**
     * Get base used amount
     *
     * @return float
     */
    public function getBaseUsedAmount()
    {
        return $this->baseUsedAmount;
    }

    /**
     * Set base used amount
     *
     * @param float $baseUsedAmount
     * @return $this
     */
    public function setBaseUsedAmount($baseUsedAmount)
    {
        $this->baseUsedAmount = $baseUsedAmount;
        return $this;
    }

    /**
     * Set used amount
     *
     * @return float
     */
    public function getUsedAmount()
    {
        return $this->usedAmount;
    }

    /**
     * Set used amount
     *
     * @param float $usedAmount
     * @return $this
     */
    public function setUsedAmount($usedAmount)
    {
        $this->usedAmount = $usedAmount;
        return $this;
    }

    /**
     * Get items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    /**
     * Set items count
     *
     * @param int $count
     * @return $this
     */
    public function setItemsCount($count)
    {
        $this->itemsCount = $count;
        return $this;
    }
}
