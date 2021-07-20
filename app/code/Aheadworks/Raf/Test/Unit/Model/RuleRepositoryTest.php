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

use Aheadworks\Raf\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Raf\Model\Rule;
use Aheadworks\Raf\Model\RuleRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Api\Data\RuleInterfaceFactory;
use Aheadworks\Raf\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Raf\Model\ResourceModel\Rule as RuleResourceModel;
use Aheadworks\Raf\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Raf\Model\ResourceModel\Rule\Collection as RuleCollection;

/**
 * Class RuleRepository
 *
 * @package Aheadworks\Raf\Test\Unit\Model
 */
class RuleRepositoryTest extends TestCase
{
    /**
     * @var RuleRepository
     */
    private $model;

    /**
     * @var RuleResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var RuleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleInterfaceFactoryMock;

    /**
     * @var RuleCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleCollectionFactoryMock;

    /**
     * @var RuleSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var array
     */
    private $ruleData = [
        'id' => 1,
        'status' => 1
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(RuleResourceModel::class, ['save', 'load', 'delete']);
        $this->ruleInterfaceFactoryMock = $this->createPartialMock(RuleInterfaceFactory::class, ['create']);
        $this->ruleCollectionFactoryMock = $this->createPartialMock(RuleCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            RuleSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);
        $this->dataObjectProcessorMock = $this->createPartialMock(DataObjectProcessor::class, ['buildOutputDataArray']);
        $this->model = $objectManager->getObject(
            RuleRepository::class,
            [
                'resource' => $this->resourceMock,
                'ruleInterfaceFactory' => $this->ruleInterfaceFactoryMock,
                'ruleCollectionFactory' => $this->ruleCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var RuleInterface|\PHPUnit_Framework_MockObject_MockObject $ruleMock */
        $ruleMock = $this->createPartialMock(Rule::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->ruleData['id']);

        $this->assertSame($ruleMock, $this->model->save($ruleMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $this->expectExceptionMessage('Exception message.');
        $this->expectException(CouldNotSaveException::class);
        $exception = new \Exception('Exception message.');

        /** @var RuleInterface|\PHPUnit_Framework_MockObject_MockObject $ruleMock */
        $ruleMock = $this->createPartialMock(Rule::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($ruleMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $ruleId = 1;

        /** @var RuleInterface|\PHPUnit_Framework_MockObject_MockObject $ruleMock */
        $ruleMock = $this->createMock(Rule::class);
        $this->ruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ruleMock, $ruleId)
            ->willReturnSelf();
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($ruleId);

        $this->assertSame($ruleMock, $this->model->get($ruleId));
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
        $ruleId = 20;
        $ruleMock = $this->createMock(Rule::class);
        $this->ruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ruleMock, $ruleId)
            ->willReturn(null);

        $this->model->get($ruleId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var RuleCollection|\PHPUnit_Framework_MockObject_MockObject $ruleCollectionMock */
        $ruleCollectionMock = $this->createPartialMock(
            RuleCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(RuleSearchResultsInterface::class);
        /** @var Rule|\PHPUnit_Framework_MockObject_MockObject $ruleModelMock */
        $ruleModelMock = $this->createPartialMock(Rule::class, ['getData']);
        /** @var RuleInterface|\PHPUnit_Framework_MockObject_MockObject $ruleMock */
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);

        $this->ruleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($ruleCollectionMock, RuleInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $ruleCollectionMock);

        $ruleCollectionMock->expects($this->once())
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

        $ruleCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$ruleModelMock]);

        $this->ruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($ruleModelMock, RuleInterface::class)
            ->willReturn($this->ruleData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($ruleMock, $this->ruleData, RuleInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$ruleMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $ruleId = '123';

        $ruleMock = $this->createMock(Rule::class);
        $ruleMock->expects($this->any())
            ->method('getId')
            ->willReturn($ruleId);
        $this->ruleInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($ruleMock, $ruleId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($ruleMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($ruleId));
    }

    /**
     * Testing of delete method on exception
     *
     * @expectedException CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->expectException(CouldNotDeleteException::class);
        $ruleMock = $this->createMock(Rule::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($ruleMock)
            ->willThrowException(new \Exception());
        $this->model->delete($ruleMock);
    }
}
