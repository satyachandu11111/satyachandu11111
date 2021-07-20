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

use Aheadworks\Raf\Model\Service\AdvocateService;
use Aheadworks\Raf\Api\TransactionManagementInterface;
use Aheadworks\Raf\Model\Advocate\Account\Checker as AccountChecker;
use Aheadworks\Raf\Model\Advocate\Account\Creator as AccountCreator;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Advocate\Url as AdvocateUrl;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Model\AdvocateSummary;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Aheadworks\Raf\Api\Data\TransactionInterface;

/**
 * Class AdvocateServiceTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Service
 */
class AdvocateServiceTest extends TestCase
{
    /**
     * @var AdvocateService
     */
    private $object;

    /**
     * @var AccountChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $accountCheckerMock;

    /**
     * @var AdvocateUrl|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateUrlMock;

    /**
     * @var AccountCreator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $accountCreatorMock;

    /***
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /***
     * @var TransactionManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->accountCheckerMock = $this->createPartialMock(
            AccountChecker::class,
            ['canParticipateInReferralProgram','canUseReferralProgramAndSpend','isParticipantOfReferralProgram']
        );

        $this->accountCreatorMock = $this->createPartialMock(
            AccountCreator::class,
            ['createAdvocate']
        );

        $this->advocateUrlMock = $this->createPartialMock(
            AdvocateUrl::class,
            ['getReferralUrl']
        );

        $this->advocateSummaryRepositoryMock = $this->getMockForAbstractClass(
            AdvocateSummaryRepositoryInterface::class
        );

        $this->transactionManagementMock = $this->getMockForAbstractClass(
            TransactionManagementInterface::class
        );

        $this->object = $objectManager->getObject(
            AdvocateService::class,
            [
                'accountChecker' => $this->accountCheckerMock,
                'accountCreator' => $this->accountCreatorMock,
                'advocateUrl' => $this->advocateUrlMock,
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock,
                'transactionManagement' => $this->transactionManagementMock
            ]
        );
    }

    /**
     * Testing of createReferralLink method
     */
    public function testCreateReferralLink()
    {
        $customerId = 2123;
        $websiteId = 1;

        $this->accountCheckerMock->expects($this->once())
            ->method('canParticipateInReferralProgram')
            ->with($customerId, $websiteId)
            ->willReturn(true);

        $this->accountCheckerMock->expects($this->once())
            ->method('isParticipantOfReferralProgram')
            ->with($customerId, $websiteId)
            ->willReturn(false);

        /** @var AdvocateSummary|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryMock */
        $advocateSummaryMock = $this->createMock(AdvocateSummary::class);

        $this->accountCreatorMock->expects($this->once())
            ->method('createAdvocate')
            ->with($customerId, $websiteId)
            ->willReturn($advocateSummaryMock);

        $this->assertSame($advocateSummaryMock, $this->object->createReferralLink($customerId, $websiteId));
    }

    /**
     * Testing of createReferralLink method in case customer is not
     *     allowed to become an advocate for some reason
     */
    public function testCreateReferralLinkWhenCustomerNotAllowed()
    {
        $customerId = 2123;
        $websiteId = 1;

        $this->accountCheckerMock->expects($this->once())
            ->method('canParticipateInReferralProgram')
            ->with($customerId, $websiteId)
            ->willReturn(false);

        $this->expectException(LocalizedException::class);
        $this->object->createReferralLink($customerId, $websiteId);
    }

    /**
     * Testing of createReferralLink method in case customer is
     *     already an advocate
     */
    public function testCreateReferralLinkWhenCustomerIsAdvocate()
    {
        $customerId = 2123;
        $websiteId = 1;

        $this->accountCheckerMock->expects($this->once())
            ->method('canParticipateInReferralProgram')
            ->with($customerId, $websiteId)
            ->willReturn(true);

        $this->accountCheckerMock->expects($this->once())
            ->method('isParticipantOfReferralProgram')
            ->with($customerId, $websiteId)
            ->willReturn(true);

        $this->expectException(LocalizedException::class);
        $this->object->createReferralLink($customerId, $websiteId);
    }

    /**
     * Testing of spendDiscountOnCheckout method
     */
    public function testSpendDiscountOnCheckout()
    {
        $customerId = 432;
        $websiteId = 2;
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafIsFriendDiscount', 'getBaseAwRafAmount', 'getBaseShippingDiscountAmount', 'getAwRafAmountType']
        );

        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);

        $this->accountCheckerMock->expects($this->once())
            ->method('canUseReferralProgramAndSpend')
            ->with($customerId, $websiteId)
            ->willReturn(true);

        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->willReturn($transactionMock);

        $orderMock->expects($this->once())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn('');

        $orderMock->expects($this->exactly(2))
            ->method('getBaseAwRafAmount')
            ->willReturn(30);
        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');

        $orderMock->expects($this->once())
            ->method('getBaseShippingDiscountAmount')
            ->willReturn(5);

        $this->assertSame(true, $this->object->spendDiscountOnCheckout($customerId, $websiteId, $orderMock));
    }

    /**
     * Testing of spendDiscountOnCheckout method on exception
     */
    public function testSpendDiscountOnCheckoutExceptionCase()
    {
        $customerId = 432;
        $websiteId = 2;
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafIsFriendDiscount', 'getBaseAwRafAmount', 'getBaseShippingDiscountAmount', 'getAwRafAmountType']
        );
        $orderMock->expects($this->once())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn('');
        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');
        $orderMock->expects($this->exactly(2))
            ->method('getBaseAwRafAmount')
            ->willReturn(30);
        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');
        $orderMock->expects($this->once())
            ->method('getBaseShippingDiscountAmount')
            ->willReturn(5);

        $this->accountCheckerMock->expects($this->once())
            ->method('canUseReferralProgramAndSpend')
            ->with($customerId, $websiteId)
            ->willReturn(true);

        $exception = new \Exception(__('Exception message.'));
        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->willThrowException($exception);

        $this->expectException(LocalizedException::class);
        $this->object->spendDiscountOnCheckout($customerId, $websiteId, $orderMock);
    }

    /**
     * Testing of refundReferralDiscountForCanceledOrder method
     */
    public function testRefundReferralDiscountForCanceledOrder()
    {
        $customerId = 432;
        $websiteId = 2;
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafIsFriendDiscount', 'getBaseAwRafAmount', 'getBaseShippingDiscountAmount', 'getAwRafAmountType']
        );

        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);
        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->willReturn($transactionMock);

        $orderMock->expects($this->once())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn('');
        $orderMock->expects($this->exactly(2))
            ->method('getBaseAwRafAmount')
            ->willReturn(30);
        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');
        $orderMock->expects($this->once())
            ->method('getBaseShippingDiscountAmount')
            ->willReturn(5);

        $this->assertSame(
            true,
            $this->object->refundReferralDiscountForCanceledOrder($customerId, $websiteId, $orderMock)
        );
    }

    /**
     * Testing of refundReferralDiscountForCanceledOrder method on exception
     */
    public function testRefundReferralDiscountForCanceledOrderExceptionCase()
    {
        $customerId = 432;
        $websiteId = 2;
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafIsFriendDiscount', 'getBaseAwRafAmount', 'getBaseShippingDiscountAmount', 'getAwRafAmountType']
        );
        $orderMock->expects($this->once())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn('');

        $orderMock->expects($this->exactly(2))
            ->method('getBaseAwRafAmount')
            ->willReturn(30);

        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');

        $orderMock->expects($this->once())
            ->method('getBaseShippingDiscountAmount')
            ->willReturn(5);

        $exception = new \Exception(__('Exception message.'));

        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->willThrowException($exception);

        $this->expectException(LocalizedException::class);
        $this->object->refundReferralDiscountForCanceledOrder($customerId, $websiteId, $orderMock);
    }

    /**
     * Testing of refundReferralDiscountForCreditmemo method
     */
    public function testRefundReferralDiscountForCreditmemo()
    {
        $customerId = 432;
        $websiteId = 2;
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafIsFriendDiscount', 'getAwRafAmountType']
        );

        $creditmemoMock = $this->createPartialMock(
            Creditmemo::class,
            ['getAwRafIsReturnToAccount', 'getBaseAwRafAmount', 'getBaseShippingDiscountAmount']
        );

        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);
        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->willReturn($transactionMock);

        $orderMock->expects($this->once())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn('');
        $creditmemoMock->expects($this->once())
            ->method('getAwRafIsReturnToAccount')
            ->willReturn(1);
        $creditmemoMock->expects($this->exactly(2))
            ->method('getBaseAwRafAmount')
            ->willReturn(30);
        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');
        $creditmemoMock->expects($this->once())
            ->method('getBaseShippingDiscountAmount')
            ->willReturn(5);

        $this->assertSame(
            true,
            $this->object->refundReferralDiscountForCreditmemo($customerId, $websiteId, $creditmemoMock, $orderMock)
        );
    }

    /**
     * Testing of refundReferralDiscountForCreditmemo method on exception
     */
    public function testRefundReferralDiscountForCreditmemoExceptionCase()
    {
        $customerId = 432;
        $websiteId = 2;
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getAwRafIsFriendDiscount', 'getAwRafAmountType']
        );

        $creditmemoMock = $this->createPartialMock(
            Creditmemo::class,
            ['getAwRafIsReturnToAccount', 'getBaseAwRafAmount', 'getBaseShippingDiscountAmount']
        );

        $orderMock->expects($this->once())
            ->method('getAwRafIsFriendDiscount')
            ->willReturn('');
        $creditmemoMock->expects($this->once())
            ->method('getAwRafIsReturnToAccount')
            ->willReturn(1);
        $creditmemoMock->expects($this->exactly(2))
            ->method('getBaseAwRafAmount')
            ->willReturn(30);
        $creditmemoMock->expects($this->once())
            ->method('getBaseShippingDiscountAmount')
            ->willReturn(5);
        $orderMock->expects($this->exactly(3))
            ->method('getAwRafAmountType')
            ->willReturn('fixed');

        $exception = new \Exception(__('Exception message.'));

        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->willThrowException($exception);

        $this->expectException(LocalizedException::class);
        $this->object->refundReferralDiscountForCreditmemo($customerId, $websiteId, $creditmemoMock, $orderMock);
    }
}
