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
namespace Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\Report;

use Aheadworks\Raf\Api\Data\OrderInterface;
use Aheadworks\Raf\Ui\DataProvider\Advocate\FriendPurchases\FilterProcessor;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order as SalesOrder;
use Magento\SalesSequence\Model\Manager;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as StateHandler;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Sales\Model\ResourceModel\Attribute;

/**
 * Class FriendPurchases
 *
 * @package Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\Report
 */
class FriendPurchases extends SalesOrder
{
    /**
     * @var FilterProcessor
     */
    private $filterProcessor;

    /**
     * @param Context $context
     * @param Attribute $attribute
     * @param Manager $sequenceManager
     * @param Snapshot $entitySnapshot
     * @param RelationComposite $entityRelationComposite
     * @param StateHandler $stateHandler
     * @param FilterProcessor $filterProcessor
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Attribute $attribute,
        Manager $sequenceManager,
        StateHandler $stateHandler,
        FilterProcessor $filterProcessor,
        $connectionName = null
    ) {
        $this->filterProcessor = $filterProcessor;
        parent::__construct(
            $context,
            $entitySnapshot,
            $entityRelationComposite,
            $attribute,
            $sequenceManager,
            $stateHandler,
            $connectionName
        );
    }

    /**
     * Retrieve top totals
     *
     * @param SearchCriteria $searchCriteria
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTopTotals($searchCriteria)
    {
        $connection = $this->getConnection();
        $websiteId = $this->filterProcessor->getWebsiteId($searchCriteria);
        $storeIds = $this->filterProcessor->getStoreIds($websiteId);
        $periodDays = 30;

        $select = $connection->select()
            ->from(
                $this->getMainTable(),
                [
                    'percent_orders' => new \Zend_Db_Expr(
                        'COALESCE(TRUNCATE(COUNT(*) / (' .
                        $this->getTotalOrderSelect($storeIds, $periodDays) .
                        ') * 100, 2), 0)'
                    ),
                    'percent_total_amount' => new \Zend_Db_Expr(
                        'COALESCE(TRUNCATE(SUM(' . OrderInterface::BASE_TOTAL_INVOICED . ') '
                        . '/ (' . $this->getTotalOrderAmountSelect($storeIds, $periodDays) .
                        ') * 100, 2), 0)'
                    )
                ]
            )->where(OrderInterface::AW_RAF_IS_ADVOCATE_REWARD_RECEIVED . ' = ?', 1);

        $this
            ->addStoreFilter($select, $storeIds)
            ->addDateFilter($select, $periodDays);

        return $connection->fetchRow($select);
    }

    /**
     * Retrieve total order select
     *
     * @param int[] $storeIds
     * @param int $days
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTotalOrderSelect($storeIds, $days)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), new \Zend_Db_Expr('COUNT(*)'));

        $this
            ->addStoreFilter($select, $storeIds)
            ->addDateFilter($select, $days);

        return $select;
    }

    /**
     * Retrieve total order amount select
     *
     * @param int[] $storeIds
     * @param int $days
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTotalOrderAmountSelect($storeIds, $days)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), new \Zend_Db_Expr('SUM(' . OrderInterface::BASE_TOTAL_INVOICED . ')'));

        $this
            ->addStoreFilter($select, $storeIds)
            ->addDateFilter($select, $days);

        return $select;
    }

    /**
     * Add date filter
     *
     * @param Select $select
     * @param int $days
     * @return $this
     */
    private function addDateFilter($select, $days)
    {
        $select->where('DATE(' . OrderInterface::CREATED_AT . ') >= CURRENT_DATE - INTERVAL ? DAY', $days);

        return $this;
    }

    /**
     * Add date filter
     *
     * @param Select $select
     * @param array $storeIds
     * @return $this
     */
    private function addStoreFilter($select, $storeIds)
    {
        $select->where(OrderInterface::STORE_ID . ' IN (?)', $storeIds);

        return $this;
    }
}
