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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Aheadworks\Raf\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_WHO_CAN_INVITE_FRIENDS = 'aw_raf/general/who_can_invite_friends';
    const XML_PATH_GENERAL_ORDER_STATUS_TO_ALLOW_INVITATION = 'aw_raf/general/order_status_to_allow_invitation';
    const XML_PATH_GENERAL_CUSTOMER_GROUPS_TO_JOIN_RAF_PROGRAM = 'aw_raf/general/customer_groups_to_join_raf_program';
    const XML_PATH_GENERAL_ORDER_STATUS_TO_GIVE_ADVOCATE_REWARD = 'aw_raf/general/order_status_to_give_advocate_reward';
    const XML_PATH_GENERAL_HOLDING_PERIOD_IN_DAYS = 'aw_raf/general/holding_period_in_days';
    const XML_PATH_GENERAL_MAXIMUM_RAF_DISCOUNT_TO_SUBTOTAL = 'aw_raf/general/maximum_raf_discount_to_subtotal';
    const XML_PATH_GENERAL_EARNED_DISCOUNT_EXPIRES_IN_DAYS = 'aw_raf/general/earned_discount_expires_in_days';
    const XML_PATH_GENERAL_SUBSEQUENT_DISCOUNTS_ALLOWED = 'aw_raf/general/subsequent_discounts_allowed';
    const XML_PATH_GENERAL_STATIC_BLOCK_FOR_WELCOME_POPUP = 'aw_raf/general/static_block_for_welcome_popup';
    const XML_PATH_GENERAL_SANDBOX_MODE = 'aw_raf/general/sandbox_mode';
    const XML_PATH_EMAIL_SENDER = 'aw_raf/email/sender';
    const XML_PATH_EMAIL_NEW_FRIEND_NOTIFICATION_TEMPLATE = 'aw_raf/email/new_friend_notification_template';
    const XML_PATH_EMAIL_SEND_EMAIL_REMINDER_IN_DAYS = 'aw_raf/email/send_email_reminder_in_days';
    const XML_PATH_EMAIL_EXPIRATION_REMINDER_TEMPLATE = 'aw_raf/email/expiration_reminder_template';
    const XML_PATH_EMAIL_EXPIRATION_TEMPLATE = 'aw_raf/email/expiration_template';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve "who can invite" type
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getWhoCanInvite($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_WHO_CAN_INVITE_FRIENDS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve order statuses to allow invitation
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getOrderStatusesToAllowInvitation($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ORDER_STATUS_TO_ALLOW_INVITATION,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve customer groups which is allowed to join referral program
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getCustomerGroupsToJoinReferralProgram($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CUSTOMER_GROUPS_TO_JOIN_RAF_PROGRAM,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve order status to give a reward to advocate
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getOrderStatusToGiveRewardToAdvocate($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ORDER_STATUS_TO_GIVE_ADVOCATE_REWARD,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve number of days for holding period
     *
     * @param int|null $websiteId
     * @return int
     */
    public function getNumberOfDaysForHoldingPeriod($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_HOLDING_PERIOD_IN_DAYS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve maximum raf discount which can be applied to subtotal
     *
     * @param int|null $websiteId
     * @return float
     */
    public function getMaximumDiscountToApplyToSubtotal($websiteId = null)
    {
        return (float)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_MAXIMUM_RAF_DISCOUNT_TO_SUBTOTAL,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve number of days, earned discount will expire
     *
     * @param int|null $websiteId
     * @return int
     */
    public function getNumberOfDaysEarnedDiscountWillExpire($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_EARNED_DISCOUNT_EXPIRES_IN_DAYS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if subsequent discounts are allowed
     *
     * @param int|null $websiteId
     * @return int
     */
    public function isSubsequentDiscountsAllowed($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SUBSEQUENT_DISCOUNTS_ALLOWED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve static block id for welcome popup
     *
     * @param int|null $storeId
     * @return int
     */
    public function getStaticBlockIdForWelcomePopup($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_STATIC_BLOCK_FOR_WELCOME_POPUP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if sandbox mode is enabled
     *
     * @return bool
     */
    public function isSandboxModeEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SANDBOX_MODE
        );
    }

    /**
     * Retrieve send email reminder in days
     *
     * @param int|null $websiteId
     * @return int
     */
    public function getSendEmailReminderInDays($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SEND_EMAIL_REMINDER_IN_DAYS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve email sender
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve email sender name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSenderName($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve email sender email
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSenderEmail($storeId = null)
    {
        $sender = $this->getEmailSender($storeId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve new friend notification template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNewFriendNotificationTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_NEW_FRIEND_NOTIFICATION_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve expiration reminder template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getExpirationReminderTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_EXPIRATION_REMINDER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve expiration template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getExpirationTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_EXPIRATION_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
