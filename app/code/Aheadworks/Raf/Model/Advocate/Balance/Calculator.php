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
namespace Aheadworks\Raf\Model\Advocate\Balance;

/**
 * Class Calculator
 *
 * @package Aheadworks\Raf\Model\Advocate\Balance
 */
class Calculator
{
    /**
     * Calculate new cumulative amount
     *
     * @param float $currentCumulativeAmount
     * @param float $amount
     * @return float
     */
    public function calculateNewCumulativeAmount($currentCumulativeAmount, $amount)
    {
        $newCumulativeAmount = $currentCumulativeAmount + $amount;

        return $newCumulativeAmount > 0 ? $newCumulativeAmount : 0;
    }
}
