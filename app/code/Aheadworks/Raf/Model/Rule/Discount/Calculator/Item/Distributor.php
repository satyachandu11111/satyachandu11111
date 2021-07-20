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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator\Item;

use Aheadworks\Raf\Model\Metadata\Rule\Discount\Item as MetadataRuleDiscountItem;
use Aheadworks\Raf\Model\Metadata\Rule\Discount\ItemFactory as MetadataRuleDiscountItemFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class Distributor
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\Item
 */
class Distributor
{
    /**
     * @var MetadataRuleDiscountItemFactory
     */
    private $metadataRuleDiscountItemFactory;

    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var array
     */
    private $roundingDelta = [];

    /**
     * @var array
     */
    private $distributeFields = [
        'amount',
        'base_amount'
    ];

    /**
     * @param MetadataRuleDiscountItemFactory $metadataRuleDiscountItemFactory
     * @param SimpleDataObjectConverter $simpleDataObjectConverter
     * @param PriceCurrencyInterface $priceCurrency
     * @param Processor $processor
     */
    public function __construct(
        MetadataRuleDiscountItemFactory $metadataRuleDiscountItemFactory,
        SimpleDataObjectConverter $simpleDataObjectConverter,
        PriceCurrencyInterface $priceCurrency,
        Processor $processor
    ) {
        $this->metadataRuleDiscountItemFactory = $metadataRuleDiscountItemFactory;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->priceCurrency = $priceCurrency;
        $this->processor = $processor;
    }

    /**
     * Distribute item if needed
     *
     * @param MetadataRuleDiscountItem $metadataRuleDiscountItem
     * @return MetadataRuleDiscountItem
     */
    public function distribute($metadataRuleDiscountItem)
    {
        $item = $metadataRuleDiscountItem->getItem();
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            return $this->reset()->distributeProcess($metadataRuleDiscountItem);
        }

        return $metadataRuleDiscountItem;
    }

    /**
     * Reset rounding data before start new process
     * @return $this
     */
    private function reset()
    {
        foreach ($this->distributeFields as $field) {
            // Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $this->roundingDelta[$field] = 0.0000001;
        }
        return $this;
    }

    /**
     * Distribute process
     *
     * @param MetadataRuleDiscountItem $metadataRuleDiscountItem
     * @return MetadataRuleDiscountItem
     */
    private function distributeProcess($metadataRuleDiscountItem)
    {
        $childrenDiscount = [];
        $item = $metadataRuleDiscountItem->getItem();
        $parentBaseRowTotal = $this->processor->getTotalItemBasePrice($item);
        foreach ($item->getChildren() as $child) {
            $ratio = $this->processor->getTotalItemBasePrice($child) / $parentBaseRowTotal;

            /** @var MetadataRuleDiscountItem $metadataRuleDiscountChild */
            $metadataRuleDiscountChild = $this->metadataRuleDiscountItemFactory->create();
            foreach ($this->distributeFields as $field) {
                $itemValue = $metadataRuleDiscountItem->{$this->generateMethodName($field, 'get')}();
                if (!$itemValue) {
                    continue;
                }

                $value = $itemValue * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $this->roundingDelta[$field]);
                $this->roundingDelta[$field] = $this->roundingDelta[$field] + $value - $roundedValue;

                $metadataRuleDiscountChild->{$this->generateMethodName($field, 'set')}($roundedValue);
            }
            if ($metadataRuleDiscountChild->getAmount()) {
                $metadataRuleDiscountChild->setItem($child);
                $childrenDiscount[] = $metadataRuleDiscountChild;
            }
        }

        $metadataRuleDiscountItem
            ->setAmount(0)
            ->setBaseAmount(0)
            ->setChildren($childrenDiscount);

        return $metadataRuleDiscountItem;
    }

    /**
     * Generate method name
     *
     * @param string $field
     * @param string $prefix
     * @return string
     */
    private function generateMethodName($field, $prefix)
    {
        return $prefix . $this->simpleDataObjectConverter->snakeCaseToUpperCamelCase($field);
    }
}
