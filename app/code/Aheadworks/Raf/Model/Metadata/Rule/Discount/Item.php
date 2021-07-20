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
namespace Aheadworks\Raf\Model\Metadata\Rule\Discount;

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class Item
 *
 * @package Aheadworks\Raf\Model\Metadata\Rule\Discount
 */
class Item
{
    /**
     * @var AbstractItem
     */
    private $item;

    /**
     * @var int
     */
    private $ruleId;

    /**
     * @var float
     */
    private $percent;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $baseAmount;

    /**
     * @var Item[]
     */
    private $children;

    /**
     * Item constructor
     */
    public function __construct()
    {
        $this
            ->setPercent(0)
            ->setAmount(0)
            ->setBaseAmount(0)
            ->setChildren([]);
    }

    /**
     * Set item
     *
     * @return AbstractItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get item
     *
     * @param AbstractItem $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get rule id
     *
     * @return int
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId)
    {
        $this->ruleId = $ruleId;
        return $this;
    }

    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
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
     * Get children
     *
     * @return Item[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children
     *
     * @param Item[] $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }
}
