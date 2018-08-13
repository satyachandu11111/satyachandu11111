<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Model\ResourceModel\CollectionUpdater;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterAbstract;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use MageWorx\OptionBase\Model\CollectionUpdate\Option\Updaters as OptionCollectionUpdaters;
use MageWorx\OptionBase\Model\CollectionUpdate\Option\Value\Updaters as ValueCollectionUpdaters;

class Value extends CollectionUpdaterAbstract
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
     * Process option value collection by updaters
     */
    public function process()
    {
        $partFrom = $this->collection->getSelect()->getPart('from');

        foreach ($this->valueCollectionUpdaters->getData() as $valueCollectionUpdatersItem) {
            $alias = $valueCollectionUpdatersItem->getTableAlias();
            if (array_key_exists($alias, $partFrom)) {
                continue;
            }

            $this->collection->getSelect()->joinLeft(
                $valueCollectionUpdatersItem->getFromConditions($this->conditions),
                $valueCollectionUpdatersItem->getOnConditionsAsString(),
                $valueCollectionUpdatersItem->getColumns()
            );
        }
    }
}
