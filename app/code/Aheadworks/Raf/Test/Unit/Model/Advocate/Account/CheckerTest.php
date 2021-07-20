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

use Aheadworks\Raf\Model\Advocate\Account\Checker;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Raf\Model\Advocate\Account\Checker\CustomerGroup as CustomerGroupChecker;
use Aheadworks\Raf\Model\Advocate\Account\Checker\CustomerInvitation as CustomerInvitationChecker;
use Aheadworks\Raf\Api\AdvocateBalanceManagementInterface;
use Aheadworks\Raf\Api\RuleManagementInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Model\AdvocateSummary;

/**
 * Class CheckerTest
 * @package Aheadworks\Raf\Model\Advocate\Account
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $object;

    /**
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /**
     * @var AdvocateBalanceManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateBalanceMock;

    /**
     * @var CustomerGroupChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupCheckerMock;

    /**
     * @var CustomerInvitationChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerInvitationCheckerMock;

    /**
     * @var RuleManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleManagementMock;

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
        $this->advocateBalanceMock = $this->getMockForAbstractClass(
            AdvocateBalanceManagementInterface::class
        );
        $this->ruleManagementMock = $this->getMockForAbstractClass(
            RuleManagementInterface::class
        );
        $this->customerGroupCheckerMock = $this->createPartialMock(
            CustomerGroupChecker::class,
            ['isCustomerInReferralProgramGroup']
        );
        $this->customerInvitationCheckerMock= $this->createPartialMock(
            CustomerInvitationChecker::class,
            ['isInvitationAllowedForCustomer']
        );

        $this->object = $objectManager->getObject(
            Checker::class,
            [
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock,
                'ruleManagement' => $this->ruleManagementMock,
                'advocateBalance' => $this->advocateBalanceMock,
                'customerGroupChecker' => $this->customerGroupCheckerMock,
                'customerInvitationChecker' => $this->customerInvitationCheckerMock
            ]
        );
    }

    /**
     * Testing of canParticipateInReferralProgram method
     *
     * @dataProvider canParticipateInReferralProgramProvider
     * @param mixed $customerId
     * @param bool $isGroupAllowed
     * @param bool $isInvitationAllowed
     * @param bool $result
     */
    public function testCanParticipateInReferralProgram($customerId, $isGroupAllowed, $isInvitationAllowed, $result)
    {
        $websiteId = 2;

        if ($customerId) {
            $this->customerGroupCheckerMock->expects($this->once())
                ->method('isCustomerInReferralProgramGroup')
                ->with($customerId, $websiteId)
                ->willReturn($isGroupAllowed);
            $this->customerInvitationCheckerMock->expects($this->any())
                ->method('isInvitationAllowedForCustomer')
                ->with($customerId, $websiteId)
                ->willReturn($isInvitationAllowed);
        }
        $this->assertSame($result, $this->object->canParticipateInReferralProgram($customerId, $websiteId));
    }

    /**
     * Data provider for testCanParticipateInReferralProgram method
     *
     * @return array
     */
    public function canParticipateInReferralProgramProvider()
    {
        return [
            'no customer at all' => ['', false, false, false],
            'invitation is not allowed' => [5, true, false, false],
            'group is not allowed' => [5, false, true, false],
            'group and invitation are allowed' => [5, true, true, true],
            'group and invitation are not allowed' => [5, false, false, false],
        ];
    }

    /**
     * Testing of canUseReferralProgramAndSpend method
     *
     * @dataProvider testCanUseReferralProgramAndSpendProvider
     * @param bool $canParticipate
     * @param bool $isBalanceAvailable
     * @param bool $isActiveRule
     * @param bool $result
     */
    public function testCanUseReferralProgramAndSpend($canParticipate, $isBalanceAvailable, $isActiveRule, $result)
    {
        $customerId = 3;
        $websiteId = 2;

        $this->customerGroupCheckerMock->expects($this->once())
            ->method('isCustomerInReferralProgramGroup')
            ->with($customerId, $websiteId)
            ->willReturn($canParticipate);
        $this->customerInvitationCheckerMock->expects($this->any())
            ->method('isInvitationAllowedForCustomer')
            ->with($customerId, $websiteId)
            ->willReturn($canParticipate);
        $this->advocateBalanceMock->expects($this->any())
            ->method('checkBalance')
            ->with($customerId, $websiteId)
            ->willReturn($isBalanceAvailable);
        $this->ruleManagementMock->expects($this->any())
            ->method('getActiveRule')
            ->with($websiteId)
            ->willReturn($isActiveRule);

        $this->assertSame($result, $this->object->canUseReferralProgramAndSpend($customerId, $websiteId));
    }

    /**
     * Data provider for testCanParticipateInReferralProgram method
     *
     * @return array
     */
    public function testCanUseReferralProgramAndSpendProvider()
    {
        return [
            'customer can participate with balance and active rule' => [true, true, true, true],
            'customer cannot participate with balance and active rule' => [false, true, true, true],
            'customer can participate with no balance and no rule' => [true, false, false, false],
            'customer can participate with no balance and active rule' => [true, false, true, true],
            'customer cannot participate with no balance and active rule' => [false, false, true, false],
            'customer cannot participate with balance and no active rule' => [false, true, false, false]
        ];
    }

    /**
     * Testing of isParticipantOfReferralProgram method
     */
    public function testIsParticipantOfReferralProgram()
    {
        $customerId = 5;
        $websiteId = 2;

        $advocateSummaryMock = $this->createMock(AdvocateSummary::class);
        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn($advocateSummaryMock);

        $this->assertSame(true, $this->object->isParticipantOfReferralProgram($customerId, $websiteId));
    }

    /**
     * Testing of isParticipantOfReferralProgram method on exception
     */
    public function testIsParticipantOfReferralProgramOnException()
    {
        $customerId = 5;
        $websiteId = 2;
        $exception = new NoSuchEntityException(__('some exception'));

        $this->advocateSummaryRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId, $websiteId)
            ->willThrowException($exception);

        $this->assertSame(false, $this->object->isParticipantOfReferralProgram($customerId, $websiteId));
    }
}
