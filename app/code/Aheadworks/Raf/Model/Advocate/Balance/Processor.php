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

use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Advocate\Balance\Calculator as BalanceCalculator;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;

/**
 * Class Processor
 *
 * @package Aheadworks\Raf\Model\Advocate\Balance
 */
class Processor
{
    /**
     * @var BalanceCalculator
     */
    protected $balanceCalculator;

    /**
     * @param Calculator $balanceCalculator
     */
    public function __construct(
        BalanceCalculator $balanceCalculator
    ) {
        $this->balanceCalculator = $balanceCalculator;
    }

    /**
     * Process balance amount
     *
     * @param TransactionInterface $transaction
     * @param AdvocateSummaryInterface $advocateSummary
     * @param string $amountType
     * @param float $amount
     */
    public function process($transaction, $advocateSummary, $amountType, $amount)
    {
        switch ($amountType) {
            case AdvocateOffType::FIXED:
                $cumulativeAmount = $advocateSummary->getCumulativeAmount();
                $balanceAmount = $this->balanceCalculator->calculateNewCumulativeAmount(
                    $cumulativeAmount,
                    $amount
                );
                $transaction->setBalanceAmount($balanceAmount);
                $transaction->setPercentBalanceAmount($advocateSummary->getCumulativePercentAmount());
                break;
            case AdvocateOffType::PERCENT:
                $cumulativeAmount = $advocateSummary->getCumulativePercentAmount();
                $percentBalanceAmount = $this->balanceCalculator->calculateNewCumulativeAmount(
                    $cumulativeAmount,
                    $amount
                );
                $transaction->setBalanceAmount($advocateSummary->getCumulativeAmount());
                $transaction->setPercentBalanceAmount($percentBalanceAmount);
                break;
        }
    }
}
