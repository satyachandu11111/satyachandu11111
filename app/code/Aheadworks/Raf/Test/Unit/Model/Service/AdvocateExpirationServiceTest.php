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

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Api\TransactionManagementInterface;
use Aheadworks\Raf\Model\Advocate\Expiration\Processor as ExpirationProcessor;
use Aheadworks\Raf\Model\Advocate\Notifier;
use Aheadworks\Raf\Model\Service\AdvocateExpirationService;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\ReminderStatus;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;
use Aheadworks\Raf\Model\Source\Transaction\Action;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class AdvocateExpirationServiceTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Service
 */
class AdvocateExpirationServiceTest extends TestCase
{
    /**
     * @var AdvocateExpirationService
     */
    private $object;

    /**
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /**
     * @var TransactionManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionManagementMock;

    /**
     * @var ExpirationProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $expirationProcessorMock;

    /**
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
        $this->advocateSummaryRepositoryMock = $this->getMockForAbstractClass(
            AdvocateSummaryRepositoryInterface::class
        );
        $this->transactionManagementMock = $this->getMockForAbstractClass(TransactionManagementInterface::class);
        $this->expirationProcessorMock = $this->createPartialMock(
            ExpirationProcessor::class,
            ['getAdvocatesBalanceToExpire', 'getAdvocatesWhichBalanceExpires']
        );
        $this->notifierMock = $this->createPartialMock(
            Notifier::class,
            ['notifyAboutBalanceExpired', 'expirationReminder']
        );

        $this->object = $objectManager->getObject(
            AdvocateExpirationService::class,
            [
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock,
                'transactionManagement' => $this->transactionManagementMock,
                'expirationProcessor' => $this->expirationProcessorMock,
                'notifier' => $this->notifierMock,
            ]
        );
    }

    /**
     * Testing of expireBalance method
     */
    public function testExpireBalance()
    {
        $advocateData = [
            AdvocateSummaryInterface::CUSTOMER_ID => 1,
            AdvocateSummaryInterface::WEBSITE_ID => 1,
            AdvocateSummaryInterface::CUMULATIVE_AMOUNT => 10
        ];
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);
        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);

        $this->expirationProcessorMock->expects($this->once())
            ->method('getAdvocatesBalanceToExpire')
            ->willReturn([$advocateSummaryMock]);

        $advocateSummaryMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($advocateData[AdvocateSummaryInterface::CUSTOMER_ID]);
        $advocateSummaryMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($advocateData[AdvocateSummaryInterface::WEBSITE_ID]);
        $advocateSummaryMock->expects($this->exactly(2))
            ->method('getCumulativeAmount')
            ->willReturn($advocateData[AdvocateSummaryInterface::CUMULATIVE_AMOUNT]);

        $this->transactionManagementMock->expects($this->once())
            ->method('createTransaction')
            ->with(
                $advocateData[AdvocateSummaryInterface::CUSTOMER_ID],
                $advocateData[AdvocateSummaryInterface::WEBSITE_ID],
                Action::EXPIRED,
                -$advocateData[AdvocateSummaryInterface::CUMULATIVE_AMOUNT],
                AdvocateOffType::FIXED,
                null,
                null,
                null
            )->willReturn($transactionMock);

        $this->notifierMock->expects($this->once())
            ->method('notifyAboutBalanceExpired')
            ->with($advocateSummaryMock, $transactionMock)
            ->willReturn(true);

        $this->object->expireBalance();
    }

    /**
     * Testing of sendExpirationReminder method
     *
     * @param bool $notified
     * @param string $reminderStatus
     * @dataProvider sendExpirationReminderDataProvider
     */
    public function testSendExpirationReminder($notified, $reminderStatus)
    {
        $advocateData = [
            AdvocateSummaryInterface::CUMULATIVE_AMOUNT => 10
        ];
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $this->expirationProcessorMock->expects($this->once())
            ->method('getAdvocatesWhichBalanceExpires')
            ->willReturn([$advocateSummaryMock]);

        $advocateSummaryMock->expects($this->once())
            ->method('getCumulativeAmount')
            ->willReturn($advocateData[AdvocateSummaryInterface::CUMULATIVE_AMOUNT]);

        $this->notifierMock->expects($this->once())
            ->method('expirationReminder')
            ->with(
                $advocateSummaryMock,
                $advocateData[AdvocateSummaryInterface::CUMULATIVE_AMOUNT],
                AdvocateOffType::FIXED
            )->willReturn($notified);

        $advocateSummaryMock->expects($this->once())
            ->method('setReminderStatus')
            ->with($reminderStatus)
            ->willReturnSelf();

        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($advocateSummaryMock)
            ->willReturn($advocateSummaryMock);

        $this->object->sendExpirationReminder();
    }

    /**
     * Data provider for SendExpirationReminder test
     *
     * @return array
     */
    public function sendExpirationReminderDataProvider()
    {
        return [[true, ReminderStatus::SENT], [false, ReminderStatus::FAILED]];
    }
}
