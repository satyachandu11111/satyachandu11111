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

use Aheadworks\Raf\Model\Metadata\Rule\Discount\Item as MetadataRuleDiscountItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Raf\Model\Metadata\Rule as MetadataRule;

/**
 * Interface ItemsCalculatorInterface
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator
 */
interface ItemsCalculatorInterface
{
    /**
     * Calculate items discount
     *
     * @param AbstractItem[] $items
     * @param MetadataRule $metadataRule
     * @return MetadataRuleDiscountItem[]
     */
    public function calculate($items, $metadataRule);
}
