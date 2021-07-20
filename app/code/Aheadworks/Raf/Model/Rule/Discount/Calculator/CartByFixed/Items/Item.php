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

use Aheadworks\Raf\Model\Metadata\Rule\Discount\Item as MetadataRuleDiscountItem;
use Aheadworks\Raf\Model\Metadata\Rule\Discount\ItemFactory as MetadataRuleDiscountItemFactory;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\Item\Processor;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\Item\Distributor;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class Item
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\CartByFixed\Items
 */
class Item
{
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var MetadataRuleDiscountItemFactory
     */
    private $metadataRuleDiscountItemFactory;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @param MetadataFactory $metadataFactory
     * @param MetadataRuleDiscountItemFactory $metadataRuleDiscountItemFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param Processor $processor
     * @param Distributor $distributor
     */
    public function __construct(
        MetadataFactory $metadataFactory,
        MetadataRuleDiscountItemFactory $metadataRuleDiscountItemFactory,
        PriceCurrencyInterface $priceCurrency,
        Processor $processor,
        Distributor $distributor
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->metadataRuleDiscountItemFactory = $metadataRuleDiscountItemFactory;
        $this->priceCurrency = $priceCurrency;
        $this->processor = $processor;
    }

    /**
     * Init
     *
     * @param AbstractItem[] $items
     * @param float $amount
     */
    public function init($items, $amount)
    {
        $this->metadata = $this->metadataFactory->create();
        $this
            ->calculateTotalAmount($items)
            ->calculateAvailableAmount($amount);
    }

    /**
     * Calculate item amount
     *
     * @param AbstractItem $item
     * @return MetadataRuleDiscountItem
     */
    public function calculateItemAmount($item)
    {
        $itemPrice = $this->processor->getTotalItemPrice($item);
        $baseItemPrice = $this->processor->getTotalItemBasePrice($item);

        $itemAmount = $this->metadata->getAvailableAmountLeft();
        $itemBaseAmount = $this->metadata->getBaseAvailableAmountLeft();
        if ($this->metadata->getItemsCount() > 1) {
            $rateForItem = $baseItemPrice / $this->metadata->getBaseItemsTotal();
            $itemBaseAmount = $this->metadata->getBaseAvailableAmount() * $rateForItem;

            $rateForItem = $itemPrice / $this->metadata->getItemsTotal();
            $itemAmount = $this->metadata->getAvailableAmount() * $rateForItem;

            $this->metadata->setItemsCount($this->metadata->getItemsCount() - 1);
        }
        $amount = min($itemAmount, $itemPrice);
        $baseAmount = min($itemBaseAmount, $baseItemPrice);

        $this->metadata
            ->setUsedAmount($this->metadata->getUsedAmount() + $amount)
            ->setBaseUsedAmount($this->metadata->getBaseUsedAmount() + $baseAmount);

        /** @var MetadataRuleDiscountItem $metadataRuleDiscountItem */
        $metadataRuleDiscountItem = $this->metadataRuleDiscountItemFactory->create();
        $metadataRuleDiscountItem
            ->setAmount($amount)
            ->setBaseAmount($baseAmount)
            ->setItem($item);

        return $metadataRuleDiscountItem;
    }

    /**
     * Calculate total amount
     *
     * @param AbstractItem[] $items
     * @return $this
     */
    private function calculateTotalAmount($items)
    {
        $totalAmount = $itemsCount = 0;
        foreach ($items as $item) {
            $totalAmount += $this->processor->getTotalItemBasePrice($item);
            $itemsCount++;
        }

        $this->metadata
            ->setBaseItemsTotal($totalAmount)
            ->setItemsTotal($this->priceCurrency->convertAndRound($totalAmount))
            ->setItemsCount($itemsCount);

        return $this;
    }

    /**
     * Calculate available amount
     *
     * @param float $amount
     * @return $this
     */
    private function calculateAvailableAmount($amount)
    {
        $baseAvailableAmount = min($this->metadata->getBaseItemsTotal(), $amount);

        $this->metadata
            ->setBaseAvailableAmount($baseAvailableAmount)
            ->setAvailableAmount($this->priceCurrency->convertAndRound($baseAvailableAmount));

        return $this;
    }
}
