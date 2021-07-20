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
namespace Aheadworks\Raf\Test\Unit\Model\Service;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\Data\OrderInterface;
use Aheadworks\Raf\Model\Service\AdvocateRewardService;
use Aheadworks\Raf\Model\Source\SubscriptionStatus;
use Aheadworks\Raf\Model\Source\Transaction\Action;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\TransactionManagementInterface;
use Aheadworks\Raf\Model\Advocate\Notifier;
use Aheadworks\Raf\Model\Advocate\Reward\Checker as RewardChecker;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary as AdvocateSummaryResource;

/**
 * Class AdvocateRewardServiceTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Service
 */
class AdvocateRewardServiceTest extends TestCase
{
    /**
     * @var AdvocateRewardService
     */
    private $object;

    /**
     * @var RewardChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rewardCheckerMock;

    /***
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /***
     * @var OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepositoryMock;

    /***
     * @var TransactionManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionManagementMock;

    /***
     * @var AdvocateSummaryResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryResourceMock;

    /***
     * @var Notifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $notifierMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->rewardCheckerMock = $this->createPartialMock(
            RewardChecker::class,
            ['canGiveRewardForFriendPurchase']
        );
        $this->advocateSummaryRepositoryMock = $this->getMockForAbstractClass(
            AdvocateSummaryRepositoryInterface::class
        );
        $this->orderRepositoryMock = $this->getMockForAbstractClass(
            OrderRepositoryInterface::class
        );
        $this->transactionManagementMock = $this->getMockForAbstractClass(
            TransactionManagementInterface::class
        );
        $this->advocateSummaryResourceMock = $this->createPartialMock(
            AdvocateSummaryResource::class,
            ['beginTransaction', 'commit', 'rollBack']
        );
        $this->notifierMock = $this->createPartialMock(
            Notifier::class,
            ['notifyAboutNewFriend']
        );

        $this->object = $objectManager->getObject(
            AdvocateRewardService::class,
            [
                'rewardChecker' => $this->rewardCheckerMock,
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock,
                'transactionManagement' => $this->transactionManagementMock,
                'orderRepository' => $this->orderRepositoryMock,
                'advocateSummaryResource' => $this->advocateSummaryResourceMock,
                'notifier' => $this->notifierMock,
            ]
        );
    }

    /**
     * Testing of giveRewardForFriendPurchase method
     */
    public function testGiveRewardForFriendPurchase()
    {
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafReferralLink', 'setAwRafIsAdvocateRewardReceived', 'getId', 'getStoreId']
        );
        $advocateMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);
        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);
        $advocateData = [
            AdvocateSummaryInterface::ID => 1,
            AdvocateSummaryInterface::CUSTOMER_ID => 1,
            AdvocateSummaryInterface::INVITED_FRIENDS => 1,
            AdvocateSummaryInterface::NEW_REWARD_SUBSCRIPTION_STATUS => SubscriptionStatus::SUBSCRIBED
        ];
        $orderData = [
            OrderInterface::ENTITY_ID => 1,
            OrderInterface::STORE_ID => 1,
            OrderInterface::AW_RAF_REFERRAL_LINK => '7E095AA95AF99F50DBEF4'
        ];
        $canGiveRewardForFriendPurchase = true;
        $websiteId = 1;
        $expected = true;

        $this->rewardCheckerMock->expects($this->once())
            ->method('canGiveRewardForFriendPurchase')
            ->with($websiteId, $orderMock)
            ->willReturn($canGiveRewardForFriendPurchase);
        $this->advocateSummaryResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();

        $orderMock->expects($this->once())
            ->method('getAwRafReferralLink')
            ->willReturn($orderData[OrderInterface::AW_RAF_REFERRAL_LINK]);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByReferralLink')
            ->with($orderData[OrderInterface::AW_RAF_REFERRAL_LINK], $websiteId)
            ->willReturn($advocateMock);

        $advocateMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($advocateData[AdvocateSummaryInterface::CUSTOMER_ID]);
        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->with(
                $advocateData[AdvocateSummaryInterface::CUSTOMER_ID],
                $websiteId,
                Action::ADVOCATE_EARNED_FOR_FRIEND_ORDER,
                null,
                null,
                null,
                null,
                $orderMock
            )->willReturn($transactionMock);

        $orderMock->expects($this->once())
            ->method('getId')
            ->willReturn($orderMock);
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($orderMock);
        $orderMock->expects($this->once())
            ->method('setAwRafIsAdvocateRewardReceived')
            ->with(true)
            ->willReturnSelf();
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->willReturn($orderMock);

        $advocateMock->expects($this->once())
            ->method('getId')
            ->willReturn($advocateData[AdvocateSummaryInterface::ID]);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($advocateData[AdvocateSummaryInterface::ID])
            ->willReturn($advocateMock);

        $advocateMock->expects($this->once())
            ->method('getInvitedFriends')
            ->willReturn($advocateData[AdvocateSummaryInterface::INVITED_FRIENDS]);
        $advocateMock->expects($this->once())
            ->method('setInvitedFriends')
            ->willReturn($advocateData[AdvocateSummaryInterface::INVITED_FRIENDS] + 1);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($advocateMock)
            ->willReturn($advocateMock);
        $this->advocateSummaryResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();

        $advocateMock->expects($this->once())
            ->method('getNewRewardSubscriptionStatus')
            ->willReturn($advocateData[AdvocateSummaryInterface::NEW_REWARD_SUBSCRIPTION_STATUS]);
        $orderMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($orderData[OrderInterface::STORE_ID]);
        $this->notifierMock->expects($this->once())
            ->method('notifyAboutNewFriend')
            ->with($advocateMock, $transactionMock, $orderData[OrderInterface::STORE_ID])
            ->willReturn(true);

        $this->assertEquals($expected, $this->object->giveRewardForFriendPurchase($websiteId, $orderMock));
    }

    /**
     * Testing of giveRewardForFriendPurchase method, validation is failed
     */
    public function testGiveRewardForFriendPurchaseValidationFailed()
    {
        $orderMock = $this->createMock(Order::class);
        $canGiveRewardForFriendPurchase = false;
        $websiteId = 1;
        $expected = false;

        $this->rewardCheckerMock->expects($this->once())
            ->method('canGiveRewardForFriendPurchase')
            ->with($websiteId, $orderMock)
            ->willReturn($canGiveRewardForFriendPurchase);

        $this->assertEquals($expected, $this->object->giveRewardForFriendPurchase($websiteId, $orderMock));
    }

    /**
     * Testing of giveRewardForFriendPurchase method on exception
     *
     * @expectedException LocalizedException
     */
    public function testGiveRewardForFriendPurchaseOnException()
    {
        $this->expectException(LocalizedException::class);
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafReferralLink']
        );
        $orderData = [
            OrderInterface::AW_RAF_REFERRAL_LINK => '7E095AA95AF99F50DBEF4'
        ];
        $exception = new \Exception(__('Exception message.'));
        $canGiveRewardForFriendPurchase = true;
        $websiteId = 1;

        $this->rewardCheckerMock->expects($this->once())
            ->method('canGiveRewardForFriendPurchase')
            ->with($websiteId, $orderMock)
            ->willReturn($canGiveRewardForFriendPurchase);
        $orderMock->expects($this->once())
            ->method('getAwRafReferralLink')
            ->willReturn($orderData[OrderInterface::AW_RAF_REFERRAL_LINK]);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByReferralLink')
            ->willThrowException($exception);
        $this->advocateSummaryResourceMock->expects($this->once())
            ->method('rollBack')
            ->willReturnSelf();

        $this->object->giveRewardForFriendPurchase($websiteId, $orderMock);
    }
}
