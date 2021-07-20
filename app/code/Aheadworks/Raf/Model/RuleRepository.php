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

use Aheadworks\Raf\Api\RuleRepositoryInterface;
use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Api\Data\RuleInterfaceFactory;
use Aheadworks\Raf\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Raf\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Raf\Model\ResourceModel\Rule as RuleResourceModel;
use Aheadworks\Raf\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RuleRepository
 *
 * @package Aheadworks\Raf\Model
 */
class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var RuleResourceModel
     */
    private $resource;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleInterfaceFactory;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var RuleSearchResultsInterfaceFactory
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
     * RuleRepository constructor.
     * @param RuleResourceModel $resource
     * @param RuleInterfaceFactory $ruleInterfaceFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        RuleResourceModel $resource,
        RuleInterfaceFactory $ruleInterfaceFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->ruleInterfaceFactory = $ruleInterfaceFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(RuleInterface $rule)
    {
        try {
            $this->resource->save($rule);
            $this->registry[$rule->getId()] = $rule;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        if (!isset($this->registry[$ruleId])) {
            /** @var RuleInterface $rule */
            $rule = $this->ruleInterfaceFactory->create();
            $this->resource->load($rule, $ruleId);
            if (!$rule->getId()) {
                throw NoSuchEntityException::singleField('id', $ruleId);
            }
            $this->registry[$ruleId] = $rule;
        }
        return $this->registry[$ruleId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\Raf\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RuleInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RuleSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Rule $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RuleInterface $rule)
    {
        try {
            $this->resource->delete($rule);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$rule->getId()])) {
            unset($this->registry[$rule->getId()]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        return $this->delete($this->get($ruleId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Rule $model
     * @return RuleInterface
     */
    private function getDataObject($model)
    {
        /** @var RuleInterface $object */
        $object = $this->ruleInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, RuleInterface::class),
            RuleInterface::class
        );
        return $object;
    }
}
