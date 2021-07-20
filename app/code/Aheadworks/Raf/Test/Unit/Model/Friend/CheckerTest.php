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
namespace Aheadworks\Raf\Test\Unit\Model\Friend;

use Aheadworks\Raf\Model\Friend\Checker;
use Aheadworks\Raf\Api\Data\FriendMetadataInterface;
use Aheadworks\Raf\Model\ResourceModel\Friend\Order as FriendOrderResource;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class CheckerTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Friend
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $object;

    /**
     * @var FriendOrderResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $friendOrderResourceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->friendOrderResourceMock = $this->createPartialMock(
            FriendOrderResource::class,
            ['getNumberOfOrders']
        );

        $this->object = $objectManager->getObject(
            Checker::class,
            [
                'friendOrderResource' => $this->friendOrderResourceMock,
            ]
        );
    }

    /**
     * Test for canApplyDiscount method
     *
     * @dataProvider canApplyDiscountProvider
     * @param int $numberOfOrders
     * @param bool $result
     */
    public function testCanApplyDiscount($numberOfOrders, $result)
    {
        $friendMetadataMock =  $this->getMockForAbstractClass(FriendMetadataInterface::class);
        $this->friendOrderResourceMock->expects($this->once())
            ->method('getNumberOfOrders')
            ->with($friendMetadataMock)
            ->willReturn($numberOfOrders);
        $this->assertSame($result, $this->object->canApplyDiscount($friendMetadataMock));
    }

    /**
     * Data provider for testCanApplyDiscount method
     *
     * @return array
     */
    public function canApplyDiscountProvider()
    {
        return [
            'case 1' => [2, false],
            'case 2' => [0, true],
        ];
    }
}
