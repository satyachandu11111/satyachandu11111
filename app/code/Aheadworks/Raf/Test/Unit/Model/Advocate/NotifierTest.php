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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate;

use Aheadworks\Raf\Model\Advocate\Notifier;
use Aheadworks\Raf\Model\Source\Rule\BaseOffType;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Transaction;
use Aheadworks\Raf\Model\Email\Sender;
use Aheadworks\Raf\Model\Advocate\Email\Processor\Amount\AmountProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\Raf\Model\Advocate\Email\Processor\Amount\Pool as EmailAmountProcessorPool;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Model\Email\EmailMetadataInterface;
use Magento\Framework\Exception\MailException;

/**
 * Class NotifierTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Advocate
 */
class NotifierTest extends TestCase
{
    /**
     * @var Notifier
     */
    private $object;

    /**
     * @var Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var EmailAmountProcessorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailAmountProcessorPoolMock;

    /**
     * @var EmailMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailMetaDataMock;

    /**
     * @var AmountProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $amountProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->senderMock = $this->createPartialMock(Sender::class, ['send']);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->emailAmountProcessorPoolMock = $this->createPartialMock(
            EmailAmountProcessorPool::class,
            ['get']
        );

        $this->object = $objectManager->getObject(
            Notifier::class,
            [
                'sender' => $this->senderMock,
                'logger' => $this->loggerMock,
                'storeManager' => $this->storeManagerMock,
                'emailAmountProcessorPool' => $this->emailAmountProcessorPoolMock
            ]
        );
    }

    /**
     * Testing of notifyAboutNewFriend method
     */
    public function testNotifyAboutNewFriend()
    {
        $storeId = 1;
        $amount = 10;
        $amountType = BaseOffType::FIXED;

        $this->initAdditionalMockData();
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $transactionMock = $this->createPartialMock(
            Transaction::class,
            ['getAmount', 'getAmountType']
        );
        $transactionMock->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $transactionMock->expects($this->once())
            ->method('getAmountType')
            ->willReturn($amountType);
        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($this->emailMetaDataMock)
            ->willReturn(true);

        $this->assertSame(
            true,
            $this->object->notifyAboutNewFriend(
                $advocateSummaryMock,
                $transactionMock,
                $storeId
            )
        );
    }

    /**
     * Testing of notifyAboutNewFriend method on exception
     */
    public function testNotifyAboutNewFriendOnException()
    {
        $storeId = 1;
        $amount = 10;
        $amountType = BaseOffType::FIXED;
        $exception = new MailException(__('some exception'));

        $this->initAdditionalMockData();
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $transactionMock = $this->createPartialMock(
            Transaction::class,
            ['getAmount', 'getAmountType']
        );
        $transactionMock->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $transactionMock->expects($this->once())
            ->method('getAmountType')
            ->willReturn($amountType);

        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($this->emailMetaDataMock)
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->assertSame(
            false,
            $this->object->notifyAboutNewFriend(
                $advocateSummaryMock,
                $transactionMock,
                $storeId
            )
        );
    }

    /**
     * Testing of notifyAboutBalanceExpired method
     */
    public function testNotifyAboutBalanceExpired()
    {
        $storeId = 1;
        $amount = 10;
        $amountType = BaseOffType::FIXED;

        $this->initAdditionalMockData();
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $transactionMock = $this->createPartialMock(
            Transaction::class,
            ['getAmount', 'getAmountType']
        );
        $transactionMock->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $transactionMock->expects($this->once())
            ->method('getAmountType')
            ->willReturn($amountType);
        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($this->emailMetaDataMock)
            ->willReturn(true);

        $this->assertSame(
            true,
            $this->object->notifyAboutBalanceExpired(
                $advocateSummaryMock,
                $transactionMock,
                $storeId
            )
        );
    }

    /**
     * Testing of notifyAboutBalanceExpired method on exception
     */
    public function testNotifyAboutBalanceExpiredOnException()
    {
        $storeId = 1;
        $amount = 10;
        $amountType = BaseOffType::FIXED;
        $exception = new MailException(__('some exception'));

        $this->initAdditionalMockData();
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $transactionMock = $this->createPartialMock(
            Transaction::class,
            ['getAmount', 'getAmountType']
        );
        $transactionMock->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $transactionMock->expects($this->once())
            ->method('getAmountType')
            ->willReturn($amountType);

        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($this->emailMetaDataMock)
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->assertSame(
            false,
            $this->object->notifyAboutBalanceExpired(
                $advocateSummaryMock,
                $transactionMock,
                $storeId
            )
        );
    }

    /**
     * Testing of expirationReminder method
     */
    public function testExpirationReminderExpired()
    {
        $storeId = 1;
        $amount = 10;
        $amountType = BaseOffType::FIXED;

        $this->initAdditionalMockData();
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($this->emailMetaDataMock)
            ->willReturn(true);

        $this->assertSame(
            true,
            $this->object->expirationReminder(
                $advocateSummaryMock,
                $amount,
                $amountType,
                $storeId
            )
        );
    }

    /**
     * Testing of expirationReminder method on exception
     */
    public function testExpirationReminderExpiredOnException()
    {
        $storeId = 1;
        $amount = 10;
        $amountType = BaseOffType::FIXED;
        $exception = new MailException(__('some exception'));

        $this->initAdditionalMockData();
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $this->senderMock->expects($this->once())
            ->method('send')
            ->with($this->emailMetaDataMock)
            ->willThrowException($exception);

        $this->assertSame(
            false,
            $this->object->expirationReminder(
                $advocateSummaryMock,
                $amount,
                $amountType,
                $storeId
            )
        );
    }

    /**
     * Init common mock data used for testing
     */
    private function initAdditionalMockData()
    {
        $this->emailMetaDataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $this->amountProcessorMock = $this->getMockForAbstractClass(AmountProcessorInterface::class);
        $this->emailAmountProcessorPoolMock->expects($this->once())
            ->method('get')
            ->willReturn($this->amountProcessorMock);
        $this->amountProcessorMock->expects($this->once())
            ->method('process')
            ->willReturn($this->emailMetaDataMock);
    }
}
