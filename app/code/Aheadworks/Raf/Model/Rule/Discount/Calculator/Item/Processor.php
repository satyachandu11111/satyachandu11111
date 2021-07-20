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

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Class Processor
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\Item
 */
class Processor
{
    /**
     * Retrieve total item price
     *
     * @param AbstractItem $item
     * @return float
     */
    public function getTotalItemPrice($item)
    {
        // Calculate parent price with discount for bundle dynamic product
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            $rowTotal = $this->getItemPrice($item) * $item->getTotalQty();
            foreach ($item->getChildren() as $child) {
                $rowTotal = $rowTotal - $child->getDiscountAmount() - $item->getAwRewardPointsAmount();
            }
        } else {
            $rowTotal = $this->getItemPrice($item) * $item->getTotalQty()
                - $item->getDiscountAmount() - $item->getAwRewardPointsAmount();
        }

        return $rowTotal;
    }

    /**
     * Retrieve total item base price
     *
     * @param AbstractItem $item
     * @return float
     */
    public function getTotalItemBasePrice($item)
    {
        // Calculate parent price with discount for bundle dynamic product
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            $baseRowTotal = $this->getItemBasePrice($item) * $item->getTotalQty();
            foreach ($item->getChildren() as $child) {
                $baseRowTotal = $baseRowTotal - $child->getBaseDiscountAmount() - $item->getBaseAwRewardPointsAmount();
            }
        } else {
            $baseRowTotal = $this->getItemBasePrice($item) * $item->getTotalQty()
                - $item->getBaseDiscountAmount() - $item->getBaseAwRewardPointsAmount();
        }

        return $baseRowTotal;
    }

    /**
     * Retrieve item price
     *
     * @param AbstractItem $item
     * @return float
     */
    private function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        $calcPrice = $item->getCalculationPrice();

        return $price === null ? $calcPrice : $price;
    }

    /**
     * Retrieve item base price
     *
     * @param AbstractItem $item
     * @return float
     */
    private function getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();

        return $price !== null ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }
}
