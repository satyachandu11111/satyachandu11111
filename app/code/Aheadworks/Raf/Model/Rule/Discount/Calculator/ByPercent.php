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
use Aheadworks\Raf\Model\Rule\Discount\Calculator\ByPercent\Items as ItemsCalculator;
use Aheadworks\Raf\Model\Rule\Discount\Calculator\ByPercent\Shipping as ShippingCalculator;

/**
 * Class ByPercent
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator
 */
class ByPercent extends AbstractCalculator implements DiscountCalculatorInterface
{
    /**
     * @param MetadataRuleDiscountFactory $metadataRuleDiscountFactory
     * @param ItemsCalculator $itemsCalculator
     * @param ShippingCalculator $shippingCalculator
     */
    public function __construct(
        MetadataRuleDiscountFactory $metadataRuleDiscountFactory,
        ItemsCalculator $itemsCalculator,
        ShippingCalculator $shippingCalculator
    ) {
        parent::__construct($metadataRuleDiscountFactory, $itemsCalculator, $shippingCalculator);
    }

    /**
     * {@inheritdoc}
     */
    protected function fixShippingDiscount($metadataRule, $baseAmountDiscount)
    {
        return $metadataRule;
    }
}
