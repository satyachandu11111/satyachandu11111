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
namespace Aheadworks\Raf\Test\Unit\Model;

use Aheadworks\Raf\Api\Data\AdvocateSummarySearchResultsInterface;
use Aheadworks\Raf\Model\AdvocateSummary;
use Aheadworks\Raf\Model\AdvocateSummaryRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterfaceFactory;
use Aheadworks\Raf\Api\Data\AdvocateSummarySearchResultsInterfaceFactory;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary as AdvocateSummaryResourceModel;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\CollectionFactory as AdvocateSummaryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\Collection as AdvocateSummaryCollection;

/**
 * Class AdvocateSummaryRepositoryTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model
 */
class AdvocateSummaryRepositoryTest extends TestCase
{
    /**
     * @var AdvocateSummaryRepository
     */
    private $model;

    /**
     * @var AdvocateSummaryResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var AdvocateSummaryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryInterfaceFactoryMock;

    /**
     * @var AdvocateSummaryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryCollectionFactoryMock;

    /**
     * @var AdvocateSummarySearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var array
     */
    private $advocateSummaryData = [
        'id' => 1,
        'cumulative_amount' => '30'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(
            AdvocateSummaryResourceModel::class,
            ['save', 'load', 'getAdvocateSummaryItemIdByCustomerId']
        );
        $this->advocateSummaryInterfaceFactoryMock = $this->createPartialMock(
            AdvocateSummaryInterfaceFactory::class,
            ['create']
        );
        $this->advocateSummaryCollectionFactoryMock = $this->createPartialMock(
            AdvocateSummaryCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            AdvocateSummarySearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            AdvocateSummaryRepository::class,
            [
                'resource' => $this->resourceMock,
                'advocateSummaryInterfaceFactory' => $this->advocateSummaryInterfaceFactoryMock,
                'advocateSummaryCollectionFactory' => $this->advocateSummaryCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var AdvocateSummaryInterface|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryMock */
        $advocateSummaryMock = $this->createPartialMock(AdvocateSummary::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $advocateSummaryMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->advocateSummaryData['id']);

        $this->assertSame($advocateSummaryMock, $this->model->save($advocateSummaryMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Exception message.');
        $exception = new \Exception('Exception message.');

        /** @var AdvocateSummaryInterface|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryMock */
        $advocateSummaryMock = $this->createPartialMock(AdvocateSummary::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($advocateSummaryMock);
    }

    /**
     * Testing of getByCustomerId method
     */
    public function testGetByCustomerId()
    {
        $advocateSummaryItemId = 2;
        $customerId = 1;
        $websiteId = 1;

        /** @var AdvocateSummaryInterface|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryMock */
        $advocateSummaryMock = $this->createMock(AdvocateSummary::class);
        $this->advocateSummaryInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($advocateSummaryMock);

        $this->resourceMock->expects($this->once())
            ->method('getAdvocateSummaryItemIdByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn($advocateSummaryItemId);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($advocateSummaryMock, $advocateSummaryItemId)
            ->willReturnSelf();

        $this->assertSame($advocateSummaryMock, $this->model->getByCustomerId($customerId, $websiteId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $this->expectExceptionMessage('No such entity with id = 20');
        $this->expectException(NoSuchEntityException::class);
        $advocateSummaryItemId = 20;
        $advocateSummaryMock = $this->createMock(AdvocateSummary::class);
        $this->advocateSummaryInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($advocateSummaryMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($advocateSummaryMock, $advocateSummaryItemId)
            ->willReturn(null);

        $this->model->get($advocateSummaryItemId);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $advocateSummaryItemId = 1;

        /** @var AdvocateSummaryInterface|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryMock */
        $advocateSummaryMock = $this->createMock(AdvocateSummary::class);
        $this->advocateSummaryInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($advocateSummaryMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($advocateSummaryMock, $advocateSummaryItemId)
            ->willReturnSelf();
        $advocateSummaryMock->expects($this->once())
            ->method('getId')
            ->willReturn($advocateSummaryItemId);

        $this->assertSame($advocateSummaryMock, $this->model->get($advocateSummaryItemId));
    }

    /**
     * Testing of getByCustomerId method on exception
     *
     * @expectedException NoSuchEntityException
     * @expectedExceptionMessage No such entity with customer_id = 156
     */
    public function testGetByCustomerIdOnException()
    {
        $this->expectExceptionMessage('No such entity with customer_id = 156');
        $this->expectException(NoSuchEntityException::class);
        $customerId = 156;
        $websiteId = 1;

        $this->resourceMock->expects($this->once())
            ->method('getAdvocateSummaryItemIdByCustomerId')
            ->with($customerId, $websiteId)
            ->willReturn(null);

        $this->model->getByCustomerId($customerId, $websiteId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var AdvocateSummaryCollection|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryCollectionMock */
        $advocateSummaryCollectionMock = $this->createPartialMock(
            AdvocateSummaryCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(AdvocateSummarySearchResultsInterface::class);
        /** @var AdvocateSummary|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryModelMock */
        $advocateSummaryModelMock = $this->createPartialMock(AdvocateSummary::class, ['getData']);
        /** @var AdvocateSummaryInterface|\PHPUnit_Framework_MockObject_MockObject $advocateSummaryMock */
        $advocateSummaryMock = $this->getMockForAbstractClass(AdvocateSummaryInterface::class);

        $this->advocateSummaryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($advocateSummaryCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($advocateSummaryCollectionMock, AdvocateSummaryInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $advocateSummaryCollectionMock);

        $advocateSummaryCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $advocateSummaryCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$advocateSummaryModelMock]);

        $this->advocateSummaryInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($advocateSummaryMock);
        $advocateSummaryModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->advocateSummaryData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($advocateSummaryMock, $this->advocateSummaryData, AdvocateSummaryInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$advocateSummaryMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
