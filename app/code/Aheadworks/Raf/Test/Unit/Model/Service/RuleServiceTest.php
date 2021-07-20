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

use Aheadworks\Raf\Model\Service\RuleService;
use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Api\RuleRepositoryInterface;
use Aheadworks\Raf\Model\Source\Rule\Status as RuleStatus;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\Data\RuleSearchResultsInterface;

/**
 * Class RuleServiceTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Service
 */
class RuleServiceTest extends TestCase
{
    /**
     * @var RuleService
     */
    private $object;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->ruleRepositoryMock = $this->getMockForAbstractClass(
            RuleRepositoryInterface::class
        );

        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            RuleService::class,
            [
                'ruleRepository' => $this->ruleRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Testing of getActiveRule method
     */
    public function testGetActiveRule()
    {
        $websiteId = 2;

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->any())
            ->method('addFilter')
            ->will($this->returnSelf());

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->searchCriteriaMock));

        $searchResultsMock = $this->getMockForAbstractClass(RuleSearchResultsInterface::class);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$ruleMock]));

        $this->ruleRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->will($this->returnValue($searchResultsMock));

        $this->assertSame($ruleMock, $this->object->getActiveRule($websiteId));
    }
}
