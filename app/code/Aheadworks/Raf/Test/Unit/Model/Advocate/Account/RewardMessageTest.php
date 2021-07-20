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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate\Account;

use Aheadworks\Raf\Model\Advocate\Account\RewardMessage;
use Aheadworks\Raf\Api\AdvocateBalanceManagementInterface;
use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Model\AdvocateSummary;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\Raf\Api\Data\TransactionSearchResultsInterface;

/**
 * Class RewardMessageTest
 *
 * @package Aheadworks\Raf\Model\Advocate\Account
 */
class RewardMessageTest extends TestCase
{
    /**
     * list of constants defined for testing
     */
    const CUSTOMER_ID = 25;
    const WEBSITE_ID = 2;

    /**
     * @var RewardMessage
     */
    private $object;

    /**
     * @var AdvocateBalanceManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateBalanceManagementMock;

    /**
     * @var TransactionSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $advocateSummaryRepository = $this->getMockForAbstractClass(
            AdvocateSummaryRepositoryInterface::class
        );
        $advocateSummaryMock = $this->createPartialMock(AdvocateSummary::class, ['getId']);
        $advocateSummaryRepository->expects($this->any())
            ->method('getByCustomerId')
            ->with(self::CUSTOMER_ID, self::WEBSITE_ID)
            ->willReturn($advocateSummaryMock);

        $transactionRepository = $this->getMockForAbstractClass(
            TransactionRepositoryInterface::class
        );
        $this->advocateBalanceManagementMock = $this->getMockForAbstractClass(
            AdvocateBalanceManagementInterface::class
        );

        $searchCriteriaBuilder = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['addFilter', 'create']
        );
        $searchCriteriaMock = $this->getMockForAbstractClass(
            SearchCriteria::class
        );
        $searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();

        $searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->searchResultsMock = $this->getMockForAbstractClass(TransactionSearchResultsInterface::class);

        $transactionRepository->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($this->searchResultsMock);

        $this->object = $objectManager->getObject(
            RewardMessage::class,
            [
                'advocateBalanceManagement' => $this->advocateBalanceManagementMock,
                'transactionRepository' => $transactionRepository,
                'advocateSummaryRepository' => $advocateSummaryRepository,
                'searchCriteriaBuilder' => $searchCriteriaBuilder,
            ]
        );
    }

    /**
     * Testing of getMessage method
     *
     * @dataProvider getMessageProvider
     * @param int $countOfTransactions
     * @param bool $isBalanceAvailable
     * @param string $result
     */
    public function testGetMessage($countOfTransactions, $isBalanceAvailable, $result)
    {
        $this->searchResultsMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn($countOfTransactions);
        $this->advocateBalanceManagementMock->expects($this->once())
            ->method('checkBalance')
            ->willReturn($isBalanceAvailable);

        $this->assertEquals($result, $this->object->getMessage(self::CUSTOMER_ID, self::WEBSITE_ID));
    }

    /**
     * Data provider for testGetMessage method
     *
     * @return array
     */
    public function getMessageProvider()
    {
        $message1 = __('Start inviting friends to get rewards!');
        $message2 = __('You\'ve got a reward! '
            . 'Now you can go shopping - it will be applied automatically on a checkout!');
        $message3 = __('Refer more friends to get rewards again!');

        return [
            'no transaction and no balance' => [0, false, $message1],
            '5 transactions and there is balance' => [5, true, $message2],
            '5 transactions and no balance' => [5, false, $message3],
            'no transactions and there is balance' => [0, true, $message2],
        ];
    }
}
