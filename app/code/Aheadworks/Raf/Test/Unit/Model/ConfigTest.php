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
namespace Aheadworks\Raf\Test\Unit\Model;

use Aheadworks\Raf\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ConfigTest
 * @package Aheadworks\Raf\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test getWhoCanInvite method
     */
    public function testGetWhoCanInvite()
    {
        $expected = 'all_customers';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_WHO_CAN_INVITE_FRIENDS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getWhoCanInvite());
    }

    /**
     * Test getOrderStatusesToAllowInvitation method
     */
    public function testGetOrderStatusesToAllowInvitation()
    {
        $expected = 'complete';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ORDER_STATUS_TO_ALLOW_INVITATION)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getOrderStatusesToAllowInvitation());
    }

    /**
     * Test getCustomerGroupsToJoinReferralProgram method
     */
    public function testGetCustomerGroupsToJoinReferralProgram()
    {
        $expected = '1,2';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_CUSTOMER_GROUPS_TO_JOIN_RAF_PROGRAM)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCustomerGroupsToJoinReferralProgram());
    }

    /**
     * Test getOrderStatusToGiveRewardToAdvocate method
     */
    public function testGetOrderStatusToGiveRewardToAdvocate()
    {
        $expected = 'canceled';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ORDER_STATUS_TO_GIVE_ADVOCATE_REWARD)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getOrderStatusToGiveRewardToAdvocate());
    }

    /**
     * Test getNumberOfDaysForHoldingPeriod method
     */
    public function testGetNumberOfDaysForHoldingPeriod()
    {
        $expected = '30';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_HOLDING_PERIOD_IN_DAYS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNumberOfDaysForHoldingPeriod());
    }

    /**
     * Test getMaximumDiscountToApplyToSubtotal method
     */
    public function testGetMaximumDiscountToApplyToSubtotal()
    {
        $expected = '40';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_MAXIMUM_RAF_DISCOUNT_TO_SUBTOTAL)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getMaximumDiscountToApplyToSubtotal());
    }

    /**
     * Test getNumberOfDaysEarnedDiscountWillExpire method
     */
    public function testGetNumberOfDaysEarnedDiscountWillExpire()
    {
        $expected = '360';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_EARNED_DISCOUNT_EXPIRES_IN_DAYS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNumberOfDaysEarnedDiscountWillExpire());
    }

    /**
     * Test isSubsequentDiscountsAllowed method
     */
    public function testIsSubsequentDiscountsAllowed()
    {
        $expected = true;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_SUBSEQUENT_DISCOUNTS_ALLOWED)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isSubsequentDiscountsAllowed());
    }

    /**
     * Test getStaticBlockIdForWelcomePopup method
     */
    public function testGetStaticBlockIdForWelcomePopup()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_STATIC_BLOCK_FOR_WELCOME_POPUP)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getStaticBlockIdForWelcomePopup());
    }

    /**
     * Test isSandboxModeEnabled method
     */
    public function testIsSandboxModeEnabled()
    {
        $expected = true;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_SANDBOX_MODE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isSandboxModeEnabled());
    }

    /**
     * Test getSendEmailReminderInDays method
     */
    public function testGetSendEmailReminderInDays()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SEND_EMAIL_REMINDER_IN_DAYS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getSendEmailReminderInDays());
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $storeId = 1;
        $expected = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSender($storeId));
    }

    /**
     * Testing of getEmailSenderName method
     */
    public function testGetEmailSenderName()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_name';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/name', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getEmailSenderName($storeId));
    }

    /**
     * Testing of getEmailSenderEmail method
     */
    public function testGetEmailSenderEmail()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_email';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/email', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->model->getEmailSenderEmail($storeId));
    }

    /**
     * Test getNewFriendNotificationTemplate method
     */
    public function testGetNewFriendNotificationTemplate()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_FRIEND_NOTIFICATION_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewFriendNotificationTemplate());
    }

    /**
     * Test getExpirationReminderTemplate method
     */
    public function testGetExpirationReminderTemplate()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_EXPIRATION_REMINDER_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getExpirationReminderTemplate());
    }

    /**
     * Test getExpirationTemplate method
     */
    public function testGetExpirationTemplate()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_EXPIRATION_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getExpirationTemplate());
    }
}
