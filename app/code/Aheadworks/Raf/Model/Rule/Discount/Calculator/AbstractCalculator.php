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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator;

use Aheadworks\Raf\Model\Metadata\Rule\DiscountFactory as MetadataRuleDiscountFactory;
use Aheadworks\Raf\Model\Metadata\Rule\Discount as MetadataRuleDiscount;
use Aheadworks\Raf\Model\Metadata\Rule\Discount\Item as MetadataRuleDiscountItem;
use Aheadworks\Raf\Model\Metadata\Rule as MetadataRule;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;

/**
 * Class AbstractCalculator
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator
 */
abstract class AbstractCalculator implements DiscountCalculatorInterface
{
    /**
     * @var MetadataRuleDiscountFactory
     */
    protected $metadataRuleDiscountFactory;

    /**
     * @var ItemsCalculatorInterface
     */
    protected $itemsCalculator;

    /**
     * @var ShippingCalculatorInterface
     */
    protected $shippingCalculator;

    /**
     * @param MetadataRuleDiscountFactory $metadataRuleDiscountFactory
     * @param ItemsCalculatorInterface $itemsCalculator
     * @param ShippingCalculatorInterface $shippingCalculator
     */
    public function __construct(
        MetadataRuleDiscountFactory $metadataRuleDiscountFactory,
        ItemsCalculatorInterface $itemsCalculator,
        ShippingCalculatorInterface $shippingCalculator
    ) {
        $this->metadataRuleDiscountFactory = $metadataRuleDiscountFactory;
        $this->itemsCalculator = $itemsCalculator;
        $this->shippingCalculator = $shippingCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate($items, $address, $metadataRule)
    {
        /** @var MetadataRuleDiscount $metadataRuleDiscount */
        $metadataRuleDiscount = $this->metadataRuleDiscountFactory->create();
        $metadataRuleDiscountItems = $this->itemsCalculator->calculate($items, $metadataRule);
        list($amount, $baseAmount) = $this->calculateItemAmount($metadataRuleDiscountItems);
        $this->fixShippingDiscount($metadataRule, $baseAmount);
        list($shippingRulePercent, $shippingAmount, $baseShippingAmount) = $this->shippingCalculator->calculate(
            $address,
            $metadataRule
        );

        $amount += $shippingAmount;
        $baseAmount += $baseShippingAmount;

        $metadataRuleDiscount
            ->setAmount($amount)
            ->setBaseAmount($baseAmount)
            ->setAmountType($metadataRule->getDiscountType())
            ->setItems($metadataRuleDiscountItems)
            ->setShippingPercent($shippingRulePercent)
            ->setShippingAmount($shippingAmount)
            ->setBaseShippingAmount($baseShippingAmount);

        if ($metadataRule->getDiscountType() == AdvocateOffType::PERCENT) {
            $metadataRuleDiscount->setPercentAmount($metadataRule->getDiscountAmount());
        }

        return $metadataRuleDiscount;
    }

    /**
     * Calculate item amount
     *
     * @param MetadataRuleDiscountItem[] $metadataRuleDiscountItems
     * @return array
     */
    protected function calculateItemAmount($metadataRuleDiscountItems)
    {
        $amount = $baseAmount = 0;
        foreach ($metadataRuleDiscountItems as $metadataRuleDiscountItem) {
            $item = $metadataRuleDiscountItem->getItem();

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($metadataRuleDiscountItem->getChildren() as $childMetadata) {
                    $amount += $childMetadata->getAmount();
                    $baseAmount += $childMetadata->getBaseAmount();
                }
            } else {
                $amount += $metadataRuleDiscountItem->getAmount();
                $baseAmount += $metadataRuleDiscountItem->getBaseAmount();
            }
        }

        return [$amount, $baseAmount];
    }

    /**
     * Fix shipping discount
     *
     * @param MetadataRule $metadataRule
     * @param float $baseAmountDiscount
     * @return MetadataRule
     */
    abstract protected function fixShippingDiscount($metadataRule, $baseAmountDiscount);
}
