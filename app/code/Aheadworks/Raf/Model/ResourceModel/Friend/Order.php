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
namespace Aheadworks\Raf\Model\ResourceModel\Friend;

use Aheadworks\Raf\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order as SalesOrder;
use Aheadworks\Raf\Api\Data\FriendMetadataInterface;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Sales\Model\ResourceModel\Attribute;
use Magento\SalesSequence\Model\Manager;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as StateHandler;
use Aheadworks\Raf\Model\Config;

/**
 * Class Order
 *
 * @package Aheadworks\Raf\Model\ResourceModel\Friend
 */
class Order extends SalesOrder
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param DbContext $context
     * @param Snapshot $entitySnapshot
     * @param RelationComposite $entityRelationComposite
     * @param Attribute $attribute
     * @param Manager $sequenceManager
     * @param StateHandler $stateHandler
     * @param Config $config
     * @param null $connectionName
     */
    public function __construct(
        DbContext $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Attribute $attribute,
        Manager $sequenceManager,
        StateHandler $stateHandler,
        Config $config,
        $connectionName = null
    ) {
        $this->config = $config;
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
     * Retrieve number of sales orders
     *
     * @param FriendMetadataInterface $friendMetadata
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNumberOfOrders($friendMetadata)
    {
        $connection = $this->getConnection();
        $orConditions = [
            OrderInterface::CUSTOMER_ID . ' = "' . $friendMetadata->getCustomerId() . '"',
            OrderInterface::CUSTOMER_EMAIL . ' = "' . $friendMetadata->getCustomerEmail() . '"'
        ];

        if (!$this->config->isSandboxModeEnabled()) {
            $orConditions[] = OrderInterface::REMOTE_IP . ' = "' . $friendMetadata->getCustomerIp() . '"';
        }

        $select = $connection->select()
            ->from($this->getMainTable(), new \Zend_Db_Expr('COUNT(*)'))
            ->where(OrderInterface::AW_RAF_IS_FRIEND_DISCOUNT . ' = ?', 1)
            ->where(implode(' OR ', $orConditions));

        return (int)$connection->fetchOne($select);
    }
}
