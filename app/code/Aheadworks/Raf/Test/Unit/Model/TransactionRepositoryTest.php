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

use Aheadworks\Raf\Api\Data\TransactionSearchResultsInterface;
use Aheadworks\Raf\Model\Transaction;
use Aheadworks\Raf\Model\TransactionRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Api\Data\TransactionInterfaceFactory;
use Aheadworks\Raf\Api\Data\TransactionSearchResultsInterfaceFactory;
use Aheadworks\Raf\Model\ResourceModel\Transaction as TransactionResourceModel;
use Aheadworks\Raf\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Raf\Model\ResourceModel\Transaction\Collection as TransactionCollection;

/**
 * Class TransactionRepository
 *
 * @package Aheadworks\Raf\Test\Unit\Model
 */
class TransactionRepositoryTest extends TestCase
{
    /**
     * @var TransactionRepository
     */
    private $model;

    /**
     * @var TransactionResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var TransactionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionInterfaceFactoryMock;

    /**
     * @var TransactionCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionCollectionFactoryMock;

    /**
     * @var TransactionSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $transactionData = [
        'id' => 1,
        'amount' => 15
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(TransactionResourceModel::class, ['save', 'load']);
        $this->transactionInterfaceFactoryMock = $this->createPartialMock(
            TransactionInterfaceFactory::class,
            ['create']
        );
        $this->transactionCollectionFactoryMock = $this->createPartialMock(
            TransactionCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            TransactionSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);
        $this->dataObjectProcessorMock = $this->createPartialMock(DataObjectProcessor::class, ['buildOutputDataArray']);
        $this->model = $objectManager->getObject(
            TransactionRepository::class,
            [
                'resource' => $this->resourceMock,
                'transactionInterfaceFactory' => $this->transactionInterfaceFactoryMock,
                'transactionCollectionFactory' => $this->transactionCollectionFactoryMock,
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
        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->createPartialMock(Transaction::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->transactionData['id']);

        $this->assertSame($transactionMock, $this->model->save($transactionMock));
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

        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->createPartialMock(Transaction::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($transactionMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $transactionId = 1;

        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->createMock(Transaction::class);
        $this->transactionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($transactionMock, $transactionId)
            ->willReturnSelf();
        $transactionMock->expects($this->once())
            ->method('getId')
            ->willReturn($transactionId);

        $this->assertSame($transactionMock, $this->model->get($transactionId));
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
        $transactionId = 20;
        $transactionMock = $this->createMock(Transaction::class);
        $this->transactionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($transactionMock, $transactionId)
            ->willReturn(null);

        $this->model->get($transactionId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var TransactionCollection|\PHPUnit_Framework_MockObject_MockObject $transactionCollectionMock */
        $transactionCollectionMock = $this->createPartialMock(
            TransactionCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(TransactionSearchResultsInterface::class);
        /** @var Transaction|\PHPUnit_Framework_MockObject_MockObject $transactionModelMock */
        $transactionModelMock = $this->createPartialMock(Transaction::class, ['getData']);
        /** @var TransactionInterface|\PHPUnit_Framework_MockObject_MockObject $transactionMock */
        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);

        $this->transactionCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($transactionCollectionMock, TransactionInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $transactionCollectionMock);

        $transactionCollectionMock->expects($this->once())
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

        $transactionCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$transactionModelMock]);

        $this->transactionInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($transactionModelMock, TransactionInterface::class)
            ->willReturn($this->transactionData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($transactionMock, $this->transactionData, TransactionInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$transactionMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }
}
