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
namespace Aheadworks\Raf\Model\Service;

use Aheadworks\Raf\Api\AdvocateBalanceManagementInterface;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\ReminderStatus;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;
use Aheadworks\Raf\Model\Source\Transaction\Action;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Aheadworks\Raf\Model\Advocate\Balance\Calculator as BalanceCalculator;
use Aheadworks\Raf\Model\Advocate\Balance\Resolver as BalanceResolver;

/**
 * Class AdvocateBalanceService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class AdvocateBalanceService implements AdvocateBalanceManagementInterface
{
    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var BalanceResolver
     */
    private $balanceResolver;

    /**
     * @var BalanceCalculator
     */
    private $balanceCalculator;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param BalanceCalculator $balanceCalculator
     * @param Config $config
     * @param BalanceResolver $balanceResolver
     */
    public function __construct(
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        BalanceCalculator $balanceCalculator,
        Config $config,
        BalanceResolver $balanceResolver
    ) {
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->balanceCalculator = $balanceCalculator;
        $this->config = $config;
        $this->balanceResolver = $balanceResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance($customerId, $websiteId)
    {
        $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
        return $this->balanceResolver->resolveCurrentBalance($advocate, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function checkBalance($customerId, $websiteId)
    {
        try {
            $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
            return ($this->balanceResolver->resolveCurrentBalance($advocate, $websiteId) > 0);
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountType($customerId, $websiteId)
    {
        $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
        return $this->balanceResolver->resolveCurrentDiscountType($advocate, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateBalance($customerId, $websiteId, $transaction)
    {
        $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
        $advocateNewCumulativeAmount = 0;

        $today = new \DateTime('today', new \DateTimeZone('UTC'));

        if ($transaction->getAmountType() == AdvocateOffType::FIXED) {
            $advocateNewCumulativeAmount = $this->balanceCalculator->calculateNewCumulativeAmount(
                $advocate->getCumulativeAmount(),
                $transaction->getAmount()
            );
            $advocate
                ->setCumulativeAmount($advocateNewCumulativeAmount)
                ->setCumulativeAmountUpdated($today->format(StdlibDateTime::DATETIME_PHP_FORMAT));
        }

        if ($transaction->getAmountType() == AdvocateOffType::PERCENT) {
            $advocateNewCumulativeAmount = $this->balanceCalculator->calculateNewCumulativeAmount(
                $advocate->getCumulativePercentAmount(),
                $transaction->getAmount()
            );
            $advocate
                ->setCumulativePercentAmount($advocateNewCumulativeAmount)
                ->setCumulativeAmountUpdated($today->format(StdlibDateTime::DATETIME_PHP_FORMAT));
        }

        $expirationDate = null;
        $numberDaysToExpire = $this->config->getNumberOfDaysEarnedDiscountWillExpire($websiteId);
        if ($advocateNewCumulativeAmount > 0 && $numberDaysToExpire) {
            $today = new \DateTime('today', new \DateTimeZone('UTC'));
            $today->add(new \DateInterval('P' . $numberDaysToExpire . 'D'));
            $today->setTime(23, 59, 59);
            $expirationDate = $today->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }
        $advocate->setExpirationDate($expirationDate);
        if ($transaction->getAction() == Action::EXPIRED) {
            $advocate->setReminderStatus(ReminderStatus::READY_TO_BE_SENT);
        }

        $this->advocateSummaryRepository->save($advocate);
        return true;
    }
}
