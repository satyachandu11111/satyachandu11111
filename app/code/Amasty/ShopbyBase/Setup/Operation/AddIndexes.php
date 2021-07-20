<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


declare(strict_types=1);

namespace Amasty\ShopbyBase\Setup\Operation;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Api\Data\FilterSettingRepositoryInterface;
use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddIndexes
{
    public function execute(SchemaSetupInterface $setup): void
    {
        $this->addFilterSettingsIndexes($setup);
        $this->addFilterSettingsOptIndexes($setup);
    }

    private function addFilterSettingsIndexes(SchemaSetupInterface $setup): void
    {
        $tableName = $setup->getTable(FilterSettingRepositoryInterface::TABLE);

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [FilterSettingInterface::SHOW_ICONS_ON_PRODUCT],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [FilterSettingInterface::SHOW_ICONS_ON_PRODUCT],
            AdapterInterface::INDEX_TYPE_INDEX
        );

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [FilterSettingInterface::IS_SEO_SIGNIFICANT],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [FilterSettingInterface::IS_SEO_SIGNIFICANT],
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }

    private function addFilterSettingsOptIndexes(SchemaSetupInterface $setup): void
    {
        $tableName = $setup->getTable(OptionSettingRepositoryInterface::TABLE);

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [OptionSettingInterface::URL_ALIAS],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [OptionSettingInterface::URL_ALIAS],
            AdapterInterface::INDEX_TYPE_INDEX
        );

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [OptionSettingInterface::STORE_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [OptionSettingInterface::STORE_ID],
            AdapterInterface::INDEX_TYPE_INDEX
        );

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [OptionSettingInterface::VALUE],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [OptionSettingInterface::VALUE],
            AdapterInterface::INDEX_TYPE_INDEX
        );

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [OptionSettingInterface::IS_FEATURED],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [OptionSettingInterface::IS_FEATURED],
            AdapterInterface::INDEX_TYPE_INDEX
        );

        $setup->getConnection()->addIndex(
            $tableName,
            $setup->getIdxName(
                $tableName,
                [OptionSettingInterface::FILTER_CODE, OptionSettingInterface::STORE_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [OptionSettingInterface::FILTER_CODE, OptionSettingInterface::STORE_ID],
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }
}
