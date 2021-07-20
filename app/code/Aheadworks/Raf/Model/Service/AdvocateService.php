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

use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Aheadworks\Raf\Api\TransactionManagementInterface;
use Aheadworks\Raf\Model\Advocate\Account\Checker as AccountChecker;
use Aheadworks\Raf\Model\Advocate\Account\Creator as AccountCreator;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;
use Aheadworks\Raf\Model\Source\Transaction\Action;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Advocate\Url as AdvocateUrl;

/**
 * Class AdvocateService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class AdvocateService implements AdvocateManagementInterface
{
    /**
     * @var AccountChecker
     */
    private $accountChecker;

    /**
     * @var AdvocateUrl
     */
    private $advocateUrl;

    /**
     * @var AccountCreator
     */
    private $accountCreator;

    /***
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /***
     * @var TransactionManagementInterface
     */
    private $transactionManagement;

    /**
     * @param AccountChecker $accountChecker
     * @param AccountCreator $accountCreator
     * @param AdvocateUrl $advocateUrl
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param TransactionManagementInterface $transactionManagement
     */
    public function __construct(
        AccountChecker $accountChecker,
        AccountCreator $accountCreator,
        AdvocateUrl $advocateUrl,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        TransactionManagementInterface $transactionManagement
    ) {
        $this->accountChecker = $accountChecker;
        $this->accountCreator = $accountCreator;
        $this->advocateUrl = $advocateUrl;
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->transactionManagement = $transactionManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function createReferralLink($customerId, $websiteId)
    {
        //@todo move to validator class which will be used in the advocate summary model
        if (!$this->canParticipateInReferralProgram($customerId, $websiteId)) {
            throw new LocalizedException(__('Advocate can not participate in the referral program.'));
        }
        if ($this->isParticipantOfReferralProgram($customerId, $websiteId)) {
            throw new LocalizedException(__('Advocate is already a participant of the referral program.'));
        }

        return $this->accountCreator->createAdvocate($customerId, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function isReferralLinkBelongsToAdvocate($referralLink, $customerId, $websiteId)
    {
        return $this->accountChecker->isReferralLinkBelongsToAdvocate($referralLink, $customerId, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function canParticipateInReferralProgram($customerId, $websiteId)
    {
        return $this->accountChecker->canParticipateInReferralProgram($customerId, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function canUseReferralProgramAndSpend($customerId, $websiteId)
    {
        return $this->accountChecker->canUseReferralProgramAndSpend($customerId, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function isParticipantOfReferralProgram($customerId, $websiteId)
    {
        return $this->accountChecker->isParticipantOfReferralProgram($customerId, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferralUrl($customerId, $websiteId)
    {
        $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
        return $this->advocateUrl->getReferralUrl($advocate->getReferralLink());
    }

    /**
     * {@inheritdoc}
     */
    public function updateNewRewardSubscriptionStatus($customerId, $websiteId, $isSubscribed)
    {
        $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
        $advocate->setNewRewardSubscriptionStatus($isSubscribed);
        $this->advocateSummaryRepository->save($advocate);

        return $advocate;
    }

    /**
     * {@inheritdoc}
     */
    public function spendDiscountOnCheckout($customerId, $websiteId, $order)
    {
        if (empty($order->getAwRafIsFriendDiscount())
            && $order->getBaseAwRafAmount() != 0
            && $this->canUseReferralProgramAndSpend($customerId, $websiteId)
        ) {
            try {
                $baseAmount = 0;
                if ($order->getAwRafAmountType() == AdvocateOffType::FIXED) {
                    $baseAmount = $order->getBaseAwRafAmount() - $order->getBaseShippingDiscountAmount();
                }
                if ($order->getAwRafAmountType() == AdvocateOffType::PERCENT) {
                    $baseAmount = $order->getAwRafPercentAmount();
                }

                $this->transactionManagement->createTransaction(
                    $customerId,
                    $websiteId,
                    Action::ADVOCATE_SPENT_DISCOUNT_ON_ORDER,
                    $baseAmount,
                    $order->getAwRafAmountType(),
                    null,
                    null,
                    $order
                );
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function refundReferralDiscountForCanceledOrder($customerId, $websiteId, $order)
    {
        if (empty($order->getAwRafIsFriendDiscount()) && $order->getBaseAwRafAmount() != 0) {
            try {
                $baseAmount = 0;
                if ($order->getAwRafAmountType() == AdvocateOffType::FIXED) {
                    $baseAmount = abs($order->getBaseAwRafAmount() - $order->getBaseShippingDiscountAmount());
                }
                if ($order->getAwRafAmountType() == AdvocateOffType::PERCENT) {
                    $baseAmount = abs($order->getAwRafPercentAmount());
                }

                $this->transactionManagement->createTransaction(
                    $customerId,
                    $websiteId,
                    Action::ADVOCATE_REFUND_DISCOUNT_FOR_CANCELED_ORDER,
                    $baseAmount,
                    $order->getAwRafAmountType(),
                    null,
                    null,
                    $order
                );
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function refundReferralDiscountForCreditmemo($customerId, $websiteId, $creditmemo, $order)
    {
        if (empty($order->getAwRafIsFriendDiscount()) && $creditmemo->getBaseAwRafAmount() != 0
            && $creditmemo->getAwRafIsReturnToAccount() == 1
        ) {
            try {
                $baseAmount = 0;
                if ($order->getAwRafAmountType() == AdvocateOffType::FIXED) {
                    $baseAmount = abs($creditmemo->getBaseAwRafAmount() - $creditmemo->getBaseShippingDiscountAmount());
                }
                if ($order->getAwRafAmountType() == AdvocateOffType::PERCENT) {
                    $baseAmount = abs($order->getAwRafPercentAmount());
                }

                $this->transactionManagement->createTransaction(
                    $customerId,
                    $websiteId,
                    Action::ADVOCATE_REFUND_DISCOUNT_FOR_CREDITMEMO,
                    $baseAmount,
                    $order->getAwRafAmountType(),
                    null,
                    null,
                    [$creditmemo, $order]
                );
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
            return true;
        }
        return false;
    }
}
