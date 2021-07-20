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
namespace Aheadworks\Raf\Model;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary as ResourceAdvocateSummary;

/**
 * Class AdvocateSummary
 * @package Aheadworks\Raf\Model
 */
class AdvocateSummary extends AbstractModel implements AdvocateSummaryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceAdvocateSummary::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getCumulativeAmount()
    {
        return $this->getData(self::CUMULATIVE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCumulativeAmount($cumulativeAmount)
    {
        return $this->setData(self::CUMULATIVE_AMOUNT, $cumulativeAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function getCumulativePercentAmount()
    {
        return $this->getData(self::CUMULATIVE_PERCENT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCumulativePercentAmount($cumulativePercentAmount)
    {
        return $this->setData(self::CUMULATIVE_PERCENT_AMOUNT, $cumulativePercentAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function getCumulativeAmountUpdated()
    {
        return $this->getData(self::CUMULATIVE_AMOUNT_UPDATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setCumulativeAmountUpdated($cumulativeAmountUpdated)
    {
        return $this->setData(self::CUMULATIVE_AMOUNT_UPDATED, $cumulativeAmountUpdated);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvitedFriends()
    {
        return $this->getData(self::INVITED_FRIENDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvitedFriends($invitedFriends)
    {
        return $this->setData(self::INVITED_FRIENDS, $invitedFriends);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationDate($expirationDate)
    {
        return $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRewardSubscriptionStatus()
    {
        return $this->getData(self::NEW_REWARD_SUBSCRIPTION_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setNewRewardSubscriptionStatus($newRewardSubscriptionStatus)
    {
        return $this->setData(self::NEW_REWARD_SUBSCRIPTION_STATUS, $newRewardSubscriptionStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferralLink()
    {
        return $this->getData(self::REFERRAL_LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setReferralLink($referralLink)
    {
        return $this->setData(self::REFERRAL_LINK, $referralLink);
    }

    /**
     * {@inheritdoc}
     */
    public function getReminderStatus()
    {
        return $this->getData(self::REMINDER_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setReminderStatus($reminderStatus)
    {
        return $this->setData(self::REMINDER_STATUS, $reminderStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\AdvocateSummaryExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
