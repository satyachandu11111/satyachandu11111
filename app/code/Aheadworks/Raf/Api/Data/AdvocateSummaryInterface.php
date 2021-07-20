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
namespace Aheadworks\Raf\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AdvocateSummaryInterface
 * @api
 */
interface AdvocateSummaryInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const WEBSITE_ID = 'website_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUMULATIVE_AMOUNT = 'cumulative_amount';
    const CUMULATIVE_PERCENT_AMOUNT = 'cumulative_percent_amount';
    const CUMULATIVE_AMOUNT_UPDATED = 'cumulative_amount_updated';
    const INVITED_FRIENDS = 'invited_friends';
    const EXPIRATION_DATE = 'expiration_date';
    const NEW_REWARD_SUBSCRIPTION_STATUS = 'new_reward_subscription_status';
    const REFERRAL_LINK = 'referral_link';
    const REMINDER_STATUS = 'reminder_status';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get website ID
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set customer name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get cumulative amount
     *
     * @return float
     */
    public function getCumulativeAmount();

    /**
     * Set cumulative amount
     *
     * @param float $cumulativeAmount
     * @return $this
     */
    public function setCumulativeAmount($cumulativeAmount);

    /**
     * Get cumulative percent amount
     *
     * @return float
     */
    public function getCumulativePercentAmount();

    /**
     * Set cumulative percent amount
     *
     * @param float $cumulativePercentAmount
     * @return $this
     */
    public function setCumulativePercentAmount($cumulativePercentAmount);

    /**
     * Get cumulative amount updated
     *
     * @return string|null
     */
    public function getCumulativeAmountUpdated();

    /**
     * Set cumulative amount updated
     *
     * @param string $cumulativeAmountUpdated
     * @return $this
     */
    public function setCumulativeAmountUpdated($cumulativeAmountUpdated);

    /**
     * Get count of invited friends
     *
     * @return int
     */
    public function getInvitedFriends();

    /**
     * Set count of invited friends
     *
     * @param int $invitedFriends
     * @return $this
     */
    public function setInvitedFriends($invitedFriends);

    /**
     * Get expiration date
     *
     * @return string|null
     */
    public function getExpirationDate();

    /**
     * Set expiration date
     *
     * @param string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate);

    /**
     * Get new reward subscription status
     *
     * @return int
     */
    public function getNewRewardSubscriptionStatus();

    /**
     * Set new reward subscription status
     *
     * @param int $newRewardSubscriptionStatus
     * @return $this
     */
    public function setNewRewardSubscriptionStatus($newRewardSubscriptionStatus);

    /**
     * Get referral link
     *
     * @return string|null
     */
    public function getReferralLink();

    /**
     * Set referral link
     *
     * @param string $referralLink
     * @return $this
     */
    public function setReferralLink($referralLink);

    /**
     * Get reminder status
     *
     * @return string
     */
    public function getReminderStatus();

    /**
     * Set reminder status
     *
     * @param string $reminderStatus
     * @return $this
     */
    public function setReminderStatus($reminderStatus);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Raf\Api\Data\AdvocateSummaryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\AdvocateSummaryExtensionInterface $extensionAttributes
    );
}
