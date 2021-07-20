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
namespace Aheadworks\Raf\Model\Advocate\Account;

use Aheadworks\Raf\Api\AdvocateBalanceManagementInterface;
use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Viewer
 *
 * @package Aheadworks\Raf\Model\Advocate\Account
 */
class RewardMessage
{
    /**
     * @var AdvocateBalanceManagementInterface
     */
    private $advocateBalanceManagement;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param AdvocateBalanceManagementInterface $advocateBalanceManagement
     * @param TransactionRepositoryInterface $transactionRepository
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param searchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AdvocateBalanceManagementInterface $advocateBalanceManagement,
        TransactionRepositoryInterface $transactionRepository,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        searchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->advocateBalanceManagement = $advocateBalanceManagement;
        $this->transactionRepository = $transactionRepository;
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get reward message
     *
     * @param $customerId
     * @param $websiteId
     * @return \Magento\Framework\Phrase|string
     */
    public function getMessage($customerId, $websiteId)
    {
        $isBalanceAvailable = $this->advocateBalanceManagement->checkBalance($customerId, $websiteId);
        $isAnyTransactionMade = $this->checkAdvocateTransactions($customerId, $websiteId);
        $message = '';
        if (!$isBalanceAvailable && !$isAnyTransactionMade) {
            $message = __('Start inviting friends to get rewards!');
        } elseif ($isBalanceAvailable) {
            $message = __('You\'ve got a reward! '
            . 'Now you can go shopping - it will be applied automatically on a checkout!');
        } elseif (!$isBalanceAvailable && $isAnyTransactionMade) {
            $message = __('Refer more friends to get rewards again!');
        }
        return $message;
    }

    /**
     * Check if advocate has made any transactions
     *
     * @param int $customerId
     * @param int $websiteId
     * @return bool
     */
    private function checkAdvocateTransactions($customerId, $websiteId)
    {
        try {
            $summaryItem = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
            if ($summaryItem) {
                $this->searchCriteriaBuilder->addFilter(TransactionInterface::SUMMARY_ID, $summaryItem->getId());
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $transactionCount = $this->transactionRepository->getList($searchCriteria)->getTotalCount();
                return (boolean) $transactionCount;
            }
        } catch (\Exception $exception) {
        }
        return false;
    }
}
