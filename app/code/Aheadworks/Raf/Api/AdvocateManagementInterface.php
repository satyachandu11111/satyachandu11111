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
namespace Aheadworks\Raf\Api;

/**
 * Interface AdvocateManagementInterface
 *
 * @package Aheadworks\Raf\Api
 */
interface AdvocateManagementInterface
{
    /**
     * Create referral link for customer
     *
     * @param int $customerId
     * @param int $websiteId
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createReferralLink($customerId, $websiteId);

    /**
     * Check if referral link belongs to advocate
     *
     * @param string $referralLink
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function isReferralLinkBelongsToAdvocate($referralLink, $customerId, $websiteId);

    /**
     * Check if the customer can participate in the referral program
     *
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function canParticipateInReferralProgram($customerId, $websiteId);

    /**
     * Check if the customer can use referral program and spend his balance
     *
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function canUseReferralProgramAndSpend($customerId, $websiteId);

    /**
     * Check if the customer is a participant of referral program
     *
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function isParticipantOfReferralProgram($customerId, $websiteId);

    /**
     * Create referral link for customer
     *
     * @param int $customerId
     * @param int $websiteId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReferralUrl($customerId, $websiteId);

    /**
     * Update new reward subscription status
     *
     * @param int $customerId
     * @param int $websiteId
     * @param int $isSubscribed
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateNewRewardSubscriptionStatus($customerId, $websiteId, $isSubscribed);

    /**
     * Spend RAF discount on checkout
     *
     * @param int $customerId
     * @param int $websiteId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function spendDiscountOnCheckout($customerId, $websiteId, $order);

    /**
     * Refund RAF discount for canceled order
     *
     * @param int $customerId
     * @param int $websiteId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function refundReferralDiscountForCanceledOrder($customerId, $websiteId, $order);

    /**
     * Refund RAF discount for credit memo
     *
     * @param int $customerId
     * @param int $websiteId
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function refundReferralDiscountForCreditmemo($customerId, $websiteId, $creditmemo, $order);
}
