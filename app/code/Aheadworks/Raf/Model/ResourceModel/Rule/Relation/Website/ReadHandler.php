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
namespace Aheadworks\Raf\Model\ResourceModel\Rule\Relation\Website;

use Aheadworks\Raf\Api\Data\RuleInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Raf\Model\ResourceModel\Rule\Relation\Website
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
     * @var string
     */
    private $ruleWebsiteTableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->ruleWebsiteTableName = $this->resourceConnection->getTableName('aw_raf_rule_website');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFoRaflParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var RuleInterface $entity */
        $entityId = (int)$entity->getId();
        if (!$entityId) {
            return $entity;
        }

        $websiteIds = $this->getWebsiteData($entityId);
        $entity->setWebsiteIds($websiteIds);

        return $entity;
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
            $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Retrieve website ids
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    public function getWebsiteData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->ruleWebsiteTableName)
            ->where('rule_id = :id');
        return $connection->fetchCol($select, ['id' => $entityId]);
    }
}
