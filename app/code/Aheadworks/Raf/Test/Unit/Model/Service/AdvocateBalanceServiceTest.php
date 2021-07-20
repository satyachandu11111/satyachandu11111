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
use Aheadworks\Raf\Model\Service\AdvocateBalanceService;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\ReminderStatus;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Model\Source\Transaction\Action;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Advocate\Balance\Calculator as BalanceCalculator;
use Aheadworks\Raf\Model\Advocate\Balance\Resolver as BalanceResolver;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class AdvocateBalanceServiceTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Service
 */
class AdvocateBalanceServiceTest extends TestCase
{
    /**
     * @var AdvocateBalanceService
     */
    private $object;

    /**
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /**
     * @var BalanceCalculator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $balanceCalculatorMock;

    /**
     * @var BalanceResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $balanceResolverMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

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
        $this->balanceCalculatorMock = $this->createPartialMock(
            BalanceCalculator::class,
            ['calculateNewCumulativeAmount']
        );
        $this->configMock = $this->createPartialMock(
            Config::class,
            ['getNumberOfDaysEarnedDiscountWillExpire']
        );
        $this->balanceResolverMock = $this->createMock(BalanceResolver::class);

        $this->object = $objectManager->getObject(
            AdvocateBalanceService::class,
            [
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock,
                'balanceCalculator' => $this->balanceCalculatorMock,
                'config' => $this->configMock,
                'balanceResolver' => $this->balanceResolverMock
            ]
        );
    }

    /**
     * Testing of getBalance method
     */
    public function testGetBalance()
    {
        $customerId = 1;
        $websiteId = 1;
        $expected = 10;

        $advocateMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn($advocateMock);

        $this->balanceResolverMock->expects($this->once())
            ->method('resolveCurrentBalance')
            ->with($advocateMock, $websiteId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getBalance($customerId, $websiteId));
    }

    /**
     * Testing of checkBalance method
     *
     * @param float $balance
     * @param bool $expected
     * @dataProvider checkBalanceDataProvider
     */
    public function testCheckBalance($balance, $expected)
    {
        $customerId = 1;
        $websiteId = 1;

        $advocateMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn($advocateMock);

        $this->balanceResolverMock->expects($this->once())
            ->method('resolveCurrentBalance')
            ->with($advocateMock, $websiteId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->checkBalance($customerId, $websiteId));
    }

    /**
     * Data provider for checkBalance
     *
     * @return array
     */
    public function checkBalanceDataProvider()
    {
        return [
            [0, false],
            [10, true]
        ];
    }

    /**
     * Testing of checkBalance method on exception
     */
    public function testCheckBalanceOnException()
    {
        $exception = new \Exception(__('Exception message.'));
        $customerId = 1;
        $websiteId = 1;
        $expected = false;

        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willThrowException($exception);

        $this->assertEquals($expected, $this->object->checkBalance($customerId, $websiteId));
    }

    /**
     * Testing of getDiscountType method
     */
    public function testGetDiscountType()
    {
        $customerId = 1;
        $websiteId = 1;
        $expected = AdvocateOffType::FIXED;
        $advocateMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn($advocateMock);

        $this->balanceResolverMock->expects($this->once())
            ->method('resolveCurrentDiscountType')
            ->with($advocateMock, $websiteId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getDiscountType($customerId, $websiteId));
    }

    /**
     * Testing of updateBalance method
     */
    public function testUpdateBalance()
    {
        $customerId = 1;
        $websiteId = 1;
        $expected = true;
        $transactionData = [
            TransactionInterface::AMOUNT_TYPE => AdvocateOffType::FIXED,
            TransactionInterface::AMOUNT => -10,
            TransactionInterface::ACTION => Action::EXPIRED
        ];
        $advocateData = [
            AdvocateSummaryInterface::CUMULATIVE_AMOUNT => 10
        ];
        $advocateNewCumulativeAmount = 0;
        $today = new \DateTime('today', new \DateTimeZone('UTC'));
        $cumulativeAmountUpdated = $today->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        $numberDaysToExpire = 5;

        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);
        $advocateMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn($advocateMock);

        $transactionMock->expects($this->exactly(2))
            ->method('getAmountType')
            ->willReturn($transactionData[TransactionInterface::AMOUNT_TYPE]);
        $advocateMock->expects($this->once())
            ->method('getCumulativeAmount')
            ->willReturn($advocateData[AdvocateSummaryInterface::CUMULATIVE_AMOUNT]);
        $transactionMock->expects($this->once())
            ->method('getAmount')
            ->willReturn($transactionData[TransactionInterface::AMOUNT]);
        $this->balanceCalculatorMock->expects($this->once())
            ->method('calculateNewCumulativeAmount')
            ->with(
                $advocateData[AdvocateSummaryInterface::CUMULATIVE_AMOUNT],
                $transactionData[TransactionInterface::AMOUNT]
            )->willReturn($advocateNewCumulativeAmount);

        $advocateMock->expects($this->once())
            ->method('setCumulativeAmount')
            ->with($advocateNewCumulativeAmount)
            ->willReturnSelf();
        $advocateMock->expects($this->once())
            ->method('setCumulativeAmountUpdated')
            ->with($cumulativeAmountUpdated)
            ->willReturnSelf();

        $this->configMock->expects($this->once())
            ->method('getNumberOfDaysEarnedDiscountWillExpire')
            ->with($websiteId)
            ->willReturn($numberDaysToExpire);
        $advocateMock->expects($this->once())
            ->method('setExpirationDate')
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('getAction')
            ->willReturn($transactionData[TransactionInterface::ACTION]);
        $advocateMock->expects($this->once())
            ->method('setReminderStatus')
            ->with(ReminderStatus::READY_TO_BE_SENT)
            ->willReturnSelf();

        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($advocateMock)
            ->willReturn($advocateMock);

        $this->assertEquals($expected, $this->object->updateBalance($customerId, $websiteId, $transactionMock));
    }
}
