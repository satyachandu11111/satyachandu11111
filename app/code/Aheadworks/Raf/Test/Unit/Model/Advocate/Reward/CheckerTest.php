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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate\Reward;

use Aheadworks\Raf\Model\Advocate\Reward\Checker;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;

/**
 * Class CheckerTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Advocate\Reward
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $object;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->advocateSummaryRepositoryMock = $this->getMockForAbstractClass(
            AdvocateSummaryRepositoryInterface::class
        );
        $this->configMock = $this->createPartialMock(
            Config::class,
            ['getOrderStatusToGiveRewardToAdvocate']
        );
        $this->object = $objectManager->getObject(
            Checker::class,
            [
                'config' => $this->configMock,
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock
            ]
        );
    }

    /**
     * Testing of canGiveRewardForFriendPurchase method
     *
     * @dataProvider canGiveRewardProvider
     * @param bool $isFriendDiscount
     * @param bool $isAdvocateRewardReceived
     * @param bool $isException
     * @param bool $result
     */
    public function testCanGiveReward($isFriendDiscount, $isAdvocateRewardReceived, $isException, $result)
    {
        $websiteId = 1;
        $orderStatus = 'pending';
        $referralLink = 'RAF1234567890';

        $orderMock = $this->createPartialMock(
            Order::class,
            [
                'getAwRafIsAdvocateRewardReceived',
                'getAwRafIsFriendDiscount',
                'getAwRafReferralLink',
                'getStatus',
                'getState'
            ]
        );

        $orderMock->expects($this->any())
            ->method('getAwRafIsAdvocateRewardReceived')
            ->willReturn($isAdvocateRewardReceived);
        $orderMock->expects($this->any())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn($isFriendDiscount);
        $orderMock->expects($this->any())
            ->method('getAwRafReferralLink')
            ->willReturn($referralLink);
        $orderMock->expects($this->any())
            ->method('getStatus')
            ->willReturn($orderStatus);
        $orderMock->expects($this->any())
            ->method('getState')
            ->willReturn(Order::STATE_COMPLETE);

        $this->configMock->expects($this->any())
            ->method('getOrderStatusToGiveRewardToAdvocate')
            ->with($websiteId)
            ->willReturn('complete');

        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        if (!$isException) {
            $this->advocateSummaryRepositoryMock->expects($this->any())
                ->method('getByReferralLink')
                ->willReturn($advocateSummaryMock);
        } else {
            $this->advocateSummaryRepositoryMock->expects($this->any())
                ->method('getByReferralLink')
                ->willThrowException(new NoSuchEntityException(__('some exception')));
        }
        $this->assertSame($result, $this->object->canGiveRewardForFriendPurchase($websiteId, $orderMock));
    }

    /**
     * Data provider for testCanGiveReward method
     *
     * @return array
     */
    public function canGiveRewardProvider()
    {
        return [
            'advocate reward is not received with friend discount' => [true, false, false, true],
            'advocate reward is not received with no friend discount' => [false, false, false, false],
            'advocate reward is received with friend discount' => [true, true, false, false],
            'referral link does not exists, exception should be thrown' => [true, true, true, false]
        ];
    }
}
