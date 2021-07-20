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
 * Class SaveHandler
 *
 * @package Aheadworks\Raf\Model\ResourceModel\Rule\Relation\Website
 */
class SaveHandler implements ExtensionInterface
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
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var RuleInterface $entity */
        $entityId = (int)$entity->getId();
        if (!$entityId) {
            return $entity;
        }

        $this->deleteByEntity($entityId);
        $websiteIdsToInsert = $this->getWebsiteIds($entity);
        $this->insertWebsiteData($websiteIdsToInsert);

        return $entity;
    }

    /**
     * Remove website ids by rule id
     *
     * @param int $ruleId
     * @return int
     * @throws \Exception
     */
    private function deleteByEntity($ruleId)
    {
        return $this->getConnection()->delete($this->ruleWebsiteTableName, ['rule_id = ?' => $ruleId]);
    }

    /**
     * Retrieve array of website data to insert
     *
     * @param RuleInterface $entity
     * @return array
     */
    private function getWebsiteIds($entity)
    {
        $websiteIds = [];
        foreach ($entity->getWebsiteIds() as $websiteId) {
            $websiteIds[] = [
                'rule_id' => (int)$entity->getId(),
                'website_id' => $websiteId
            ];
        }
        return $websiteIds;
    }

    /**
     * Insert website ids
     *
     * @param array $websiteIdsToInsert
     * @return $this
     * @throws \Exception
     */
    private function insertWebsiteData($websiteIdsToInsert)
    {
        if (!empty($websiteIdsToInsert)) {
            $this->getConnection()->insertMultiple($this->ruleWebsiteTableName, $websiteIdsToInsert);
        }
        return $this;
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
}
