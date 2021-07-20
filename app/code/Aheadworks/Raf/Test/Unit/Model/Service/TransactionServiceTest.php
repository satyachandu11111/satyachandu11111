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
namespace Aheadworks\Raf\Model\Service;

use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Api\AdvocateBalanceManagementInterface;
use Aheadworks\Raf\Model\Transaction\Processor\Pool as TransactionProcessorPool;
use Aheadworks\Raf\Model\ResourceModel\Transaction as TransactionResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Model\Source\Transaction\Action as TransactionAction;
use Aheadworks\Raf\Model\Source\Rule\BaseOffType;
use Aheadworks\Raf\Model\Source\Transaction\Status;
use Aheadworks\Raf\Model\Transaction;
use Aheadworks\Raf\Model\Transaction\Processor\BaseProcessor;
use PHPUnit\Framework\TestCase;

/**
 * Class TransactionServiceTest
 *
 * @package Aheadworks\Raf\Model\Service
 */
class TransactionServiceTest extends TestCase
{
    /**
     * @var TransactionService
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TransactionRepositoryInterface
     */
    private $transactionRepositoryMock;

    /**
     * @var TransactionProcessorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionProcessorPoolMock;

    /**
     * @var AdvocateBalanceManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateBalanceManagementMock;

    /**
     * @var TransactionResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionResourceMock;

    /**
     * @var BaseProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $baseProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->transactionResourceMock = $this->createPartialMock(
            TransactionResource::class,
            ['beginTransaction', 'commit', 'rollBack']
        );

        $this->transactionRepositoryMock = $this->getMockBuilder(TransactionRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();

        $this->transactionProcessorPoolMock = $this->getMockBuilder(TransactionProcessorPool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getByAction'])
            ->getMockForAbstractClass();

        $this->advocateBalanceManagementMock = $this->getMockBuilder(AdvocateBalanceManagementInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['updateBalance'])
            ->getMockForAbstractClass();

        $this->baseProcessorMock = $this->getMockBuilder(BaseProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods(['process'])
            ->getMockForAbstractClass();

        $this->object = $objectManager->getObject(
            TransactionService::class,
            [
                'transactionRepository' => $this->transactionRepositoryMock,
                'transactionProcessorPool' => $this->transactionProcessorPoolMock,
                'advocateBalanceManagement' => $this->advocateBalanceManagementMock,
                'transactionResource' => $this->transactionResourceMock,
            ]
        );
    }

    /**
     * Test createTransaction method
     */
    public function testCreateTransactionMethod()
    {
        $customerId = 3;
        $websiteId = 1;
        $action = TransactionAction::ADJUSTED_BY_ADMIN;
        $amount = '10';
        $amountType = BaseOffType::FIXED;
        $createdBy = 1;
        $adminComment = 'admin comment';

        $transactionMock = $this->createPartialMock(Transaction::class, ['getId']);

        $this->transactionProcessorPoolMock->expects($this->once())
            ->method('getByAction')
            ->with($action)
            ->willReturn($this->baseProcessorMock);

        $this->baseProcessorMock->expects($this->once())
            ->method('process')
            ->willReturn($transactionMock);

        $this->assertSame($transactionMock, $this->object->createTransaction(
            $customerId,
            $websiteId,
            $action,
            $amount,
            $amountType,
            $createdBy,
            $adminComment,
            null
        ));
    }

    /**
     * Test createTransaction method on exception
     */
    public function testCreateTransactionOnException()
    {
        $customerId = 3;
        $websiteId = 1;
        $action = TransactionAction::ADJUSTED_BY_ADMIN;
        $amount = '10';
        $amountType = BaseOffType::FIXED;
        $createdBy = 1;
        $adminComment = 'admin comment';
        $exceptionMessage = 'some exception';

        $transactionMock = $this->createPartialMock(Transaction::class, ['getId', 'getStatus']);

        $this->transactionProcessorPoolMock->expects($this->once())
            ->method('getByAction')
            ->with($action)
            ->willReturn($this->baseProcessorMock);

        $this->baseProcessorMock->expects($this->once())
            ->method('process')
            ->willReturn($transactionMock);

        $transactionMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(Status::COMPLETE);

        $this->advocateBalanceManagementMock->expects($this->once())
            ->method('updateBalance')
            ->with($customerId, $websiteId, $transactionMock)
            ->willThrowException(new \Exception($exceptionMessage));

        $this->expectException(LocalizedException::class, __($exceptionMessage));

        $this->transactionResourceMock->expects($this->once())
            ->method('rollBack');

        $this->object->createTransaction(
            $customerId,
            $websiteId,
            $action,
            $amount,
            $amountType,
            $createdBy,
            $adminComment,
            null
        );
    }
}
