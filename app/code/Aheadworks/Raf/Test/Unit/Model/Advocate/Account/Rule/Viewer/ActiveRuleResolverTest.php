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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate\Account\Rule\Viewer;

use Aheadworks\Raf\Model\Advocate\Account\Rule\Viewer\ActiveRuleResolver;
use Aheadworks\Raf\Api\RuleManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Raf\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class ActiveRuleResolverTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Advocate\Account\Rule\Viewer
 */
class ActiveRuleResolverTest extends TestCase
{
    /**
     * Constant defined for testing
     */
    const WEBSITE_ID = 1;

    /**
     * @var ActiveRuleResolver
     */
    private $object;

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

        $this->ruleManagementMock = $this->getMockForAbstractClass(
            RuleManagementInterface::class
        );
        $storeManagerMock = $this->getMockForAbstractClass(
            StoreManagerInterface::class
        );
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn(self::WEBSITE_ID);
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->object = $objectManager->getObject(
            ActiveRuleResolver::class,
            [
                'ruleManagement' => $this->ruleManagementMock,
                'storeManager' => $storeManagerMock,
            ]
        );
    }

    /**
     * Test for getRule method
     */
    public function testGetRule()
    {
        $storeId = 2;

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $this->ruleManagementMock->expects($this->once())
            ->method('getActiveRule')
            ->with(self::WEBSITE_ID)
            ->willReturn($ruleMock);
        $this->assertSame($ruleMock, $this->object->getRule($storeId));
    }
}
