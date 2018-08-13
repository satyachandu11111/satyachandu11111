<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Model\ResourceModel\CollectionUpdater;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\Option\Collection;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterAbstract;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use MageWorx\OptionBase\Model\CollectionUpdate\Option\Updaters as OptionCollectionUpdaters;
use MageWorx\OptionBase\Model\CollectionUpdate\Option\Value\Updaters as ValueCollectionUpdaters;

class Option extends CollectionUpdaterAbstract
{
    /**
     * @param ResourceConnection $resource
     * @param Collection $collection
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param OptionCollectionUpdaters $optionCollectionUpdaters
     * @param ValueCollectionUpdaters $valueCollectionUpdaters
     * @param array $conditions
     */
    public function __construct(
        ResourceConnection $resource,
        Collection $collection,
        CollectionUpdaterRegistry $collectionUpdaterRegistry,
        OptionCollectionUpdaters $optionCollectionUpdaters,
        ValueCollectionUpdaters $valueCollectionUpdaters,
        $conditions = []
    ) {
        $this->connection = $resource->getConnection();
        parent::__construct(
            $collection,
            $resource,
            $collectionUpdaterRegistry,
            $optionCollectionUpdaters,
            $valueCollectionUpdaters,
            $conditions
        );
    }

    /**
     * Process option collection by updaters
     */
    public function process()
    {
        $partFrom = $this->collection->getSelect()->getPart('from');

        foreach ($this->optionCollectionUpdaters->getData() as $optionCollectionUpdatersItem) {
            $alias = $optionCollectionUpdatersItem->getTableAlias();
            if (array_key_exists($alias, $partFrom)) {
                continue;
            }

            $this->collection->getSelect()->joinLeft(
                $optionCollectionUpdatersItem->getFromConditions($this->conditions),
                $optionCollectionUpdatersItem->getOnConditionsAsString(),
                $optionCollectionUpdatersItem->getColumns()
            );
        }
    }
}
