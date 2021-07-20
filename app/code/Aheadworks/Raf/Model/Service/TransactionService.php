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
use Aheadworks\Raf\Api\TransactionManagementInterface;
use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Model\Transaction\Processor\Pool as TransactionProcessorPool;
use Aheadworks\Raf\Model\ResourceModel\Transaction as TransactionResource;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Raf\Model\Source\Transaction\Status as TransactionStatus;

/**
 * Class TransactionService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class TransactionService implements TransactionManagementInterface
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var TransactionProcessorPool
     */
    private $transactionProcessorPool;

    /**
     * @var AdvocateBalanceManagementInterface
     */
    private $advocateBalanceManagement;

    /**
     * @var TransactionResource
     */
    private $transactionResource;

    /**
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionProcessorPool $transactionProcessorPool
     * @param AdvocateBalanceManagementInterface $advocateBalanceManagement
     * @param TransactionResource $transactionResource
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionProcessorPool $transactionProcessorPool,
        AdvocateBalanceManagementInterface $advocateBalanceManagement,
        TransactionResource $transactionResource
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionProcessorPool = $transactionProcessorPool;
        $this->advocateBalanceManagement = $advocateBalanceManagement;
        $this->transactionResource = $transactionResource;
    }

    /**
     * {@inheritdoc}
     */
    public function createTransaction(
        $customerId,
        $websiteId,
        $action,
        $amount,
        $amountType,
        $createdBy,
        $adminComment,
        $entities
    ) {
        $processor = $this->transactionProcessorPool->getByAction($action);
        $transaction = $processor->process(
            $customerId,
            $websiteId,
            $action,
            $amount,
            $amountType,
            $createdBy,
            $adminComment,
            $entities
        );

        try {
            $this->transactionResource->beginTransaction();
            $this->transactionRepository->save($transaction);
            if ($transaction->getStatus() == TransactionStatus::COMPLETE) {
                $this->advocateBalanceManagement->updateBalance($customerId, $websiteId, $transaction);
            }
            $this->transactionResource->commit();
        } catch (\Exception $e) {
            $this->transactionResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return $transaction;
    }
}
