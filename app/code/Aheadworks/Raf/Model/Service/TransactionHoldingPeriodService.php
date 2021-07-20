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

use Aheadworks\Raf\Api\TransactionHoldingPeriodManagementInterface;
use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Model\ResourceModel\Transaction as TransactionResource;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Raf\Model\Transaction\HoldingPeriod\Processor as HoldingPeriodProcessor;
use Aheadworks\Raf\Model\Transaction\HoldingPeriod\Balance as HoldingPeriodBalance;
use Aheadworks\Raf\Model\Source\Transaction\Status as TransactionStatus;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class TransactionHoldingPeriodService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class TransactionHoldingPeriodService implements TransactionHoldingPeriodManagementInterface
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var HoldingPeriodProcessor
     */
    private $holdingPeriodProcessor;

    /**
     * @var TransactionResource
     */
    private $transactionResource;

    /**
     * @var StdlibDateTime
     */
    private $dateTime;

    /**
     * @var HoldingPeriodBalance
     */
    private $holdingPeriodBalance;

    /**
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionResource $transactionResource
     * @param HoldingPeriodProcessor $holdingPeriodProcessor
     * @param HoldingPeriodBalance $holdingPeriodBalance
     * @param StdlibDateTime $dateTime
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionResource $transactionResource,
        HoldingPeriodProcessor $holdingPeriodProcessor,
        HoldingPeriodBalance $holdingPeriodBalance,
        StdlibDateTime $dateTime
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionResource = $transactionResource;
        $this->holdingPeriodProcessor = $holdingPeriodProcessor;
        $this->holdingPeriodBalance = $holdingPeriodBalance;
        $this->dateTime = $dateTime;
    }

    /**
     * @inheritdoc
     */
    public function processExpiredTransactions()
    {
        $expiredTransactions = $this->holdingPeriodProcessor->getExpiredTransactions();
        foreach ($expiredTransactions as $expiredTransaction) {
            try {
                $this->transactionResource->beginTransaction();
                $this->holdingPeriodBalance->update($expiredTransaction);

                $createdAt = new \DateTime('now');
                $expiredTransaction->setCreatedAt($this->dateTime->formatDate($createdAt));
                $expiredTransaction->setStatus(TransactionStatus::COMPLETE);

                $this->transactionRepository->save($expiredTransaction);
                $this->transactionResource->commit();
            } catch (\Exception $e) {
                $this->transactionResource->rollBack();
                throw new LocalizedException(__($e->getMessage()));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function cancelTransactionForCanceledOrder($order)
    {
        if ($order->getAwRafIsFriendDiscount()) {
            $pendingTransactionId = $this->transactionResource->getTransactionIdCreatedForFriendOrder(
                $order->getEntityId()
            );

            if ($pendingTransactionId) {
                $transaction = $this->transactionRepository->get($pendingTransactionId);
                $transaction->setStatus(TransactionStatus::CANCELED);
                $this->transactionRepository->save($transaction);
            }
        }
    }
}
