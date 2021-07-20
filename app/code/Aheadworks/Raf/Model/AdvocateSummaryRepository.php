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

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterfaceFactory;
use Aheadworks\Raf\Api\Data\AdvocateSummarySearchResultsInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummarySearchResultsInterfaceFactory;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary as AdvocateSummaryResourceModel;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\CollectionFactory as AdvocateSummaryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AdvocateSummaryRepository
 *
 * @package Aheadworks\Raf\Model
 */
class AdvocateSummaryRepository implements AdvocateSummaryRepositoryInterface
{
    /**
     * @var AdvocateSummaryResourceModel
     */
    private $resource;

    /**
     * @var AdvocateSummaryInterfaceFactory
     */
    private $advocateSummaryInterfaceFactory;

    /**
     * @var AdvocateSummaryCollectionFactory
     */
    private $advocateSummaryCollectionFactory;

    /**
     * @var AdvocateSummarySearchResultsInterfaceFactory
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
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByCustomerId = [];

    /**
     * @var array
     */
    private $registryByReferralLink = [];

    /**
     * AdvocateSummaryRepository constructor.
     * @param AdvocateSummaryResourceModel $resource
     * @param AdvocateSummaryInterfaceFactory $advocateSummaryInterfaceFactory
     * @param AdvocateSummaryCollectionFactory $advocateSummaryCollectionFactory
     * @param AdvocateSummarySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        AdvocateSummaryResourceModel $resource,
        AdvocateSummaryInterfaceFactory $advocateSummaryInterfaceFactory,
        AdvocateSummaryCollectionFactory $advocateSummaryCollectionFactory,
        AdvocateSummarySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->advocateSummaryInterfaceFactory = $advocateSummaryInterfaceFactory;
        $this->advocateSummaryCollectionFactory = $advocateSummaryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AdvocateSummaryInterface $advocateSummaryItem)
    {
        try {
            $this->resource->save($advocateSummaryItem);
            $this->registry[$advocateSummaryItem->getId()] = $advocateSummaryItem;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $advocateSummaryItem;
    }

    /**
     * {@inheritdoc}
     */
    public function get($advocateSummaryItemId)
    {
        if (!isset($this->registry[$advocateSummaryItemId])) {
            /** @var AdvocateSummaryInterface $advocateSummaryItem */
            $advocateSummaryItem = $this->advocateSummaryInterfaceFactory->create();
            $this->resource->load($advocateSummaryItem, $advocateSummaryItemId);
            if (!$advocateSummaryItem->getId()) {
                throw NoSuchEntityException::singleField('id', $advocateSummaryItemId);
            }
            $this->registry[$advocateSummaryItemId] = $advocateSummaryItem;
        }
        return $this->registry[$advocateSummaryItemId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCustomerId($customerId, $websiteId)
    {
        $cacheKey = implode('-', [$customerId, $websiteId]);
        if (!isset($this->registryByCustomerId[$cacheKey])) {
            $advocateSummaryItemId = $this->resource->getAdvocateSummaryItemIdByCustomerId($customerId, $websiteId);
            if (!$advocateSummaryItemId) {
                throw NoSuchEntityException::singleField('customer_id', $customerId);
            }
            /** @var AdvocateSummaryInterface $advocateSummaryItem */
            $advocateSummaryItem = $this->advocateSummaryInterfaceFactory->create();
            $this->resource->load($advocateSummaryItem, $advocateSummaryItemId);
            $this->registry[$advocateSummaryItemId] = $advocateSummaryItem;
            $this->registryByCustomerId[$cacheKey] = $advocateSummaryItem;
        }
        return $this->registryByCustomerId[$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getByReferralLink($referralLink, $websiteId)
    {
        $cacheKey = implode('-', [$referralLink, $websiteId]);
        if (!isset($this->registryByReferralLink[$cacheKey])) {
            $advocateSummaryItemId = $this->resource->getAdvocateSummaryItemIdByReferralLink($referralLink, $websiteId);
            if (!$advocateSummaryItemId) {
                throw NoSuchEntityException::singleField('referral_link', $referralLink);
            }
            /** @var AdvocateSummaryInterface $advocateSummaryItem */
            $advocateSummaryItem = $this->advocateSummaryInterfaceFactory->create();
            $this->resource->load($advocateSummaryItem, $advocateSummaryItemId);
            $this->registry[$advocateSummaryItemId] = $advocateSummaryItem;
            $this->registryByReferralLink[$cacheKey] = $advocateSummaryItem;
        }
        return $this->registryByReferralLink[$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\Collection $collection */
        $collection = $this->advocateSummaryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, AdvocateSummaryInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var AdvocateSummarySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var AdvocateSummary $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param AdvocateSummary $model
     * @return AdvocateSummaryInterface
     */
    private function getDataObject($model)
    {
        /** @var AdvocateSummaryInterface $object */
        $object = $this->advocateSummaryInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            AdvocateSummaryInterface::class
        );
        return $object;
    }
}
