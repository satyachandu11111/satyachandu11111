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
namespace Aheadworks\Raf\Test\Unit\Model\Rule\Discount\Customer\Friend;

use Aheadworks\Raf\Model\Rule\Discount\Customer\Friend\Validator;
use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Aheadworks\Raf\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Raf\Api\FriendManagementInterface;
use Aheadworks\Raf\Model\Metadata\Friend\Builder as FriendMetadataBuilder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\Data\FriendMetadataInterface;
use Magento\Store\Model\Store;

/**
 * Class ValidatorTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Rule\Discount\Customer\Friend
 */
class ValidatorTest extends TestCase
{
    /**
     * Validator
     */
    private $object = null;

    /**
     * @var AdvocateManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateManagementMock;

    /**
     * @var FriendManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $friendManagementMock;

    /**
     * @var FriendMetadataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $friendMetadataBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->advocateManagementMock = $this->getMockForAbstractClass(
            AdvocateManagementInterface::class
        );
        $this->friendManagementMock = $this->getMockForAbstractClass(
            FriendManagementInterface::class
        );
        $this->friendMetadataBuilderMock = $this->createPartialMock(
            FriendMetadataBuilder::class,
            ['build']
        );

        $this->object = $objectManager->getObject(
            Validator::class,
            [
                'advocateManagement' => $this->advocateManagementMock,
                'friendManagement' => $this->friendManagementMock,
                'friendMetadataBuilder' => $this->friendMetadataBuilderMock
            ]
        );
    }

    /**
     * Test for isValid method
     *
     * @dataProvider isValidDataProvider
     * @param int $customerId
     * @param bool $isParticipant
     * @param bool $isRafLinkAvailable
     * @param bool $isRegRequire
     * @param bool $result
     */
    public function testIsValid($customerId, $isParticipant, $isRafLinkAvailable, $isRegRequire, $result)
    {
        $websiteId = 1;

        $quote = $this->createPartialMock(
            Quote::class,
            [
                'getAwRafReferralLink',
                'getAwRafRuleToApply',
                'getCustomerId',
                'getStore'
            ]
        );
        $rule = $this->getMockForAbstractClass(RuleInterface::class);
        $rule->expects($this->any())
            ->method('isRegistrationRequired')
            ->willReturn($isRegRequire);
        $rule->expects($this->any())
            ->method('getFriendOff')
            ->willReturn(20);
        $quote->expects($this->once())
            ->method('getAwRafRuleToApply')
            ->willReturn($rule);
        $store = $this->createPartialMock(
            Store::class,
            ['getWebsiteId']
        );
        $store->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $quote->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $quote->expects($this->once())
            ->method('getStore')
            ->willReturn($store);

        $this->advocateManagementMock->expects($this->any())
            ->method('isReferralLinkBelongsToAdvocate')
            ->with($isRafLinkAvailable, $customerId, $websiteId)
            ->willReturn($isParticipant);
        $quote->expects($this->any())
            ->method('getAwRafReferralLink')
            ->willReturn($isRafLinkAvailable);
        $friendMetaData = $this->getMockForAbstractClass(FriendMetadataInterface::class);
        $this->friendMetadataBuilderMock->expects($this->any())
            ->method('build')
            ->with($quote)
            ->willReturn($friendMetaData);
        $this->friendManagementMock->expects($this->any())
            ->method('canApplyDiscount')
            ->with($friendMetaData)
            ->willReturn(true);

        $this->assertSame($result, $this->object->isValid($quote));
    }

    /**
     * Data provider for testIsValid method
     */
    public function isValidDataProvider()
    {
        return [
            'everything is OK' => [1, false, true, true, true],
            'customer is participant of raf program' => [1, true, true, true, false],
            'customer id is not specified' => [null, false, true, false, true],
            'raf link is not available' => [1, false, false, false, false]
        ];
    }
}
