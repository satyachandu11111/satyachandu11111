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
namespace Aheadworks\Raf\Model;

use Aheadworks\Raf\Api\TransactionRepositoryInterface;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Api\Data\TransactionInterfaceFactory;
use Aheadworks\Raf\Api\Data\TransactionSearchResultsInterface;
use Aheadworks\Raf\Api\Data\TransactionSearchResultsInterfaceFactory;
use Aheadworks\Raf\Model\ResourceModel\Transaction as TransactionResourceModel;
use Aheadworks\Raf\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class TransactionRepository
 *
 * @package Aheadworks\Raf\Model
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var TransactionResourceModel
     */
    private $resource;

    /**
     * @var TransactionInterfaceFactory
     */
    private $transactionInterfaceFactory;

    /**
     * @var TransactionCollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var TransactionSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $registry = [];
    
    /**
     * TransactionRepository constructor.
     * @param TransactionResourceModel $resource
     * @param TransactionInterfaceFactory $transactionInterfaceFactory
     * @param TransactionCollectionFactory $transactionCollectionFactory
     * @param TransactionSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        TransactionResourceModel $resource,
        TransactionInterfaceFactory $transactionInterfaceFactory,
        TransactionCollectionFactory $transactionCollectionFactory,
        TransactionSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->transactionInterfaceFactory = $transactionInterfaceFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TransactionInterface $transaction)
    {
        try {
            $this->resource->save($transaction);
            $this->registry[$transaction->getId()] = $transaction;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function get($transactionId)
    {
        if (!isset($this->registry[$transactionId])) {
            /** @var TransactionInterface $transaction */
            $transaction = $this->transactionInterfaceFactory->create();
            $this->resource->load($transaction, $transactionId);
            if (!$transaction->getId()) {
                throw NoSuchEntityException::singleField('id', $transactionId);
            }
            $this->registry[$transactionId] = $transaction;
        }
        return $this->registry[$transactionId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Raf\Model\ResourceModel\Transaction\Collection $collection */
        $collection = $this->transactionCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TransactionInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TransactionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Transaction $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Transaction $model
     * @return TransactionInterface
     */
    private function getDataObject($model)
    {
        /** @var TransactionInterface $object */
        $object = $this->transactionInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, TransactionInterface::class),
            TransactionInterface::class
        );
        return $object;
    }
}
