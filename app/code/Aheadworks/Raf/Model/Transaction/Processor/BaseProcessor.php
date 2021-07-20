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
namespace Aheadworks\Raf\Model\Transaction\Processor;

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\Data\TransactionInterfaceFactory;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Model\Transaction\Processor\Resolver\Entity;
use Aheadworks\Raf\Model\Transaction\Processor\Resolver\HoldingPeriodExpiration;
use Aheadworks\Raf\Model\Transaction\Comment\Processor as CommentProcessor;
use Aheadworks\Raf\Model\Advocate\Balance\Processor as BalanceProcessor;
use Aheadworks\Raf\Model\Source\Transaction\Status as TransactionStatus;

/**
 * Class BaseProcessor
 *
 * @package Aheadworks\Raf\Model\Transaction\Processor
 */
class BaseProcessor implements ProcessorInterface
{
    /**
     * @var TransactionInterfaceFactory
     */
    protected $transactionFactory;

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    protected $advocateSummaryRepository;

    /**
     * @var Entity
     */
    protected $entityResolver;

    /**
     * @var HoldingPeriodExpiration
     */
    protected $holdingPeriodResolver;

    /**
     * @var CommentProcessor
     */
    protected $commentProcessor;

    /**
     * @var BalanceProcessor
     */
    protected $balanceProcessor;

    /**
     * @param TransactionInterfaceFactory $transactionFactory
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param Entity $entityResolver
     * @param CommentProcessor $commentProcessor
     * @param BalanceProcessor $balanceProcessor
     * @param HoldingPeriodExpiration $holdingPeriodResolver
     */
    public function __construct(
        TransactionInterfaceFactory $transactionFactory,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        Entity $entityResolver,
        CommentProcessor $commentProcessor,
        BalanceProcessor $balanceProcessor,
        HoldingPeriodExpiration $holdingPeriodResolver
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->entityResolver = $entityResolver;
        $this->commentProcessor = $commentProcessor;
        $this->balanceProcessor = $balanceProcessor;
        $this->holdingPeriodResolver = $holdingPeriodResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process(
        $customerId,
        $websiteId,
        $action,
        $amount,
        $amountType,
        $createdBy,
        $adminComment,
        $entities
    ) {
        $advocateSummary = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);

        $transactionEntities = $this->entityResolver->resolve($entities);
        if (!$adminComment) {
            $placeholder = $this->commentProcessor->getPlaceholder($action);
            $comment = $this->commentProcessor->renderComment($action, $transactionEntities, false);
        } else {
            $placeholder = $comment = $adminComment;
        }

        /** @var TransactionInterface $transaction */
        $transaction = $this->transactionFactory->create();

        $this->balanceProcessor->process($transaction, $advocateSummary, $amountType, $amount);

        $transaction
            ->setSummaryId($advocateSummary->getId())
            ->setAction($action)
            ->setAmount($amount)
            ->setAmountType($amountType)
            ->setStatus(TransactionStatus::COMPLETE)
            ->setCreatedBy($createdBy)
            ->setEntities($transactionEntities)
            ->setAdminCommentPlaceholder($placeholder)
            ->setAdminComment($comment);

        return $transaction;
    }
}
