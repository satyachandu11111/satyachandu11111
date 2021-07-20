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

use Aheadworks\Raf\Api\AdvocateRewardManagementInterface;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\TransactionManagementInterface;
use Aheadworks\Raf\Model\Advocate\Notifier;
use Aheadworks\Raf\Model\Source\SubscriptionStatus;
use Aheadworks\Raf\Model\Source\Transaction\Action;
use Aheadworks\Raf\Model\Advocate\Reward\Checker as RewardChecker;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary as AdvocateSummaryResource;

/**
 * Class AdvocateRewardService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class AdvocateRewardService implements AdvocateRewardManagementInterface
{
    /**
     * @var RewardChecker
     */
    private $rewardChecker;

    /***
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /***
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /***
     * @var TransactionManagementInterface
     */
    private $transactionManagement;

    /***
     * @var AdvocateSummaryResource
     */
    private $advocateSummaryResource;

    /***
     * @var Notifier
     */
    private $notifier;

    /**
     * @param RewardChecker $rewardChecker
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param TransactionManagementInterface $transactionManagement
     * @param AdvocateSummaryResource $advocateSummaryResource
     * @param Notifier $notifier
     */
    public function __construct(
        RewardChecker $rewardChecker,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        OrderRepositoryInterface $orderRepository,
        TransactionManagementInterface $transactionManagement,
        AdvocateSummaryResource $advocateSummaryResource,
        Notifier $notifier
    ) {
        $this->rewardChecker = $rewardChecker;
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->orderRepository = $orderRepository;
        $this->transactionManagement = $transactionManagement;
        $this->advocateSummaryResource = $advocateSummaryResource;
        $this->notifier = $notifier;
    }

    /**
     * {@inheritdoc}
     */
    public function giveRewardForFriendPurchase($websiteId, $order)
    {
        if ($this->rewardChecker->canGiveRewardForFriendPurchase($websiteId, $order)) {
            try {
                $this->advocateSummaryResource->beginTransaction();
                $advocate = $this->advocateSummaryRepository->getByReferralLink(
                    $order->getAwRafReferralLink(),
                    $websiteId
                );
                $transaction = $this->transactionManagement->createTransaction(
                    $advocate->getCustomerId(),
                    $websiteId,
                    Action::ADVOCATE_EARNED_FOR_FRIEND_ORDER,
                    null,
                    null,
                    null,
                    null,
                    $order
                );
                $order = $this->orderRepository->get($order->getId());
                $order->setAwRafIsAdvocateRewardReceived(true);
                $this->orderRepository->save($order);
                $advocate = $this->advocateSummaryRepository->get($advocate->getId());

                $advocate->setInvitedFriends($advocate->getInvitedFriends() + 1);
                $this->advocateSummaryRepository->save($advocate);
                $this->advocateSummaryResource->commit();
            } catch (\Exception $e) {
                $this->advocateSummaryResource->rollBack();
                throw new LocalizedException(__($e->getMessage()));
            }
            if ($advocate->getNewRewardSubscriptionStatus() == SubscriptionStatus::SUBSCRIBED) {
                $this->notifier->notifyAboutNewFriend($advocate, $transaction, $order->getStoreId());
            }
            return true;
        }
        return false;
    }
}
