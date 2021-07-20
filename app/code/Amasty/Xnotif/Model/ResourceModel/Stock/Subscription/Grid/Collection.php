<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


declare(strict_types=1);

namespace Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\Grid;

use Amasty\Xnotif\Model\ResourceModel\Stock\Subscription\CollectionFactory as SubscriptionCollectionFactory;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var SubscriptionCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        SubscriptionCollectionFactory $collectionFactory,
        $mainTable,
        $resourceModel = null,
        $identifierName = null,
        $connectionName = null
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );
    }

    /**
     * @return Collection|void
     * @throws \Zend_Db_Select_Exception
     */
    public function _initSelect()
    {
        $collection = $this->collectionFactory->create();
        $wherePart = $this->getSelect()->getPart(Select::WHERE);
        $collection->_renderFiltersBefore();
        $this->_select = $collection->getSelect();
        $this->_select->setPart(Select::WHERE, $wherePart);
    }
}
