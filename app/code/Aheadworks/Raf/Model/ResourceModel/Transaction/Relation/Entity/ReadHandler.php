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
namespace Aheadworks\Raf\Model\ResourceModel\Transaction\Relation\Entity;

use Aheadworks\Raf\Api\Data\TransactionEntityInterface;
use Aheadworks\Raf\Api\Data\TransactionEntityInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Raf\Model\ResourceModel\Transaction\Relation\Entity
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TransactionEntityInterfaceFactory
     */
    private $entityFactory;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param TransactionEntityInterfaceFactory $entityFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        TransactionEntityInterfaceFactory $entityFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->entityFactory = $entityFactory;
        $this->tableName = $this->resourceConnection->getTableName('aw_raf_transaction_entity');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $entities = $this->getEntityObjects($entity->getId());
        $entity->setEntities($entities);

        return $entity;
    }

    /**
     * Retrieve entity objects
     *
     * @param int $transactionId
     * @return TransactionEntityInterface[]
     * @throws \Exception
     */
    private function getEntityObjects($transactionId)
    {
        $objects = [];
        $entities = $this->getEntities($transactionId);
        foreach ($entities as $entity) {
            /** @var TransactionEntityInterface $entityFactory */
            $entityObject = $this->entityFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $entityObject,
                $entity,
                TransactionEntityInterface::class
            );
            $objects[] = $entityObject;
        }
        return $objects;
    }

    /**
     * Retrieve entities
     *
     * @param int $transactionId
     * @return array
     * @throws \Exception
     */
    private function getEntities($transactionId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName)
            ->where('transaction_id = :transaction_id');
        return $connection->fetchAssoc($select, ['transaction_id' => $transactionId]);
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TransactionInterface::class)->getEntityConnectionName()
        );
    }
}
