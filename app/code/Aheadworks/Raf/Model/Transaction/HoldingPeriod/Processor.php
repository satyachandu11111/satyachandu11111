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
namespace Aheadworks\Raf\Model\Transaction\HoldingPeriod;

use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Raf\Model\Source\Transaction\Status as TransactionStatus;

/**
 * Class Processor
 *
 * @package Aheadworks\Raf\Model\Transaction\HoldingPeriod
 */
class Processor
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param TransactionRepositoryInterface $transactionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve transactions ready to complete
     *
     * @return TransactionInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getExpiredTransactions()
    {
        $this->searchCriteriaBuilder
            ->addFilter(TransactionInterface::STATUS, TransactionStatus::PENDING)
            ->addFilter(TransactionInterface::HOLDING_PERIOD_EXPIRATION, new \Zend_Db_Expr('NOW()'), 'lt');
        $expiredTransactions = $this->transactionRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        return $expiredTransactions;
    }
}
