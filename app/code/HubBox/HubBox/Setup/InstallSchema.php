<?php
/**
 * HubBox Click and Collect
 * Copyright (C) 2017  2017
 *
 * This file is part of HubBox/HubBox.
 *
 * HubBox/HubBox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace HubBox\HubBox\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();
        /**
         * HubBox Quote
         */
        $hubbox_quote = $setup->getConnection()->newTable($setup->getTable('hubbox_quote'));

        $hubbox_quote->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
            ],
            'Entity ID'
        );

        $hubbox_quote->addColumn('hubbox_collect_point_id', Table::TYPE_TEXT, 100, [
                'default' => '', 'nullable' => true,
                'comment' => 'HubBox Collect Point ID'
            ]
        );

        $hubbox_quote->addColumn(
            'quote_id', Table::TYPE_TEXT, 100,
            [
                'name' => 'quote_id',
                'default' => '',
                'nullable' => true,
                'comment' => 'Magento quote ID'
            ]
        );

        $hubbox_quote->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        );

        $hubbox_quote->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );

        $setup->getConnection()->createTable($hubbox_quote);

        /**
         * HubBox Order
         */
        $hubbox_order = $setup->getConnection()->newTable($setup->getTable('hubbox_sales_order'));

        $hubbox_order->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
            ],
            'Entity ID'
        );

        $hubbox_order->addColumn(
            'hubbox_collect_point_id', Table::TYPE_TEXT, 100,
            [
                'default' => '',
                'nullable' => true,
                'comment' => 'HubBox Collect Point ID'
            ]
        );

        $hubbox_order->addColumn(
            'hubbox_collection_code', Table::TYPE_TEXT, 100,
            [
                'default' => '',
                'nullable' => true,
                'comment' => 'HubBox Collection Code'
            ]
        );

        $hubbox_order->addColumn(
            'hubbox_parcel_id', Table::TYPE_TEXT, 100,
            [
                'default' => '',
                'nullable' => true,
                'comment' => 'HubBox Parcel ID'
            ]
        );

        $hubbox_order->addColumn(
            'order_id', Table::TYPE_TEXT, 100,
            [
                'default' => '',
                'nullable' => true,
                'comment' => 'Magento order ID'
            ]
        );

        $hubbox_order->addColumn(
            'processed', Table::TYPE_INTEGER,1,
            [
                'default' => 0
            ],
            'Sent to HubBox?'
        );

        $hubbox_order->addColumn(
            'attempts', Table::TYPE_INTEGER,1,
            [
                'default' => 0
            ],
            'How many times did we try?'
        );

        $hubbox_order->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        );

       $hubbox_order->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
       );

        $setup->getConnection()->createTable($hubbox_order);

        /**
         * HubBox Auth table
         */
        $hubbox_auth = $setup->getConnection()->newTable($setup->getTable('hubbox_auth'));

        $hubbox_auth->addColumn(
            'auth_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        );

        $hubbox_auth->addColumn(
            'access_token',
            Table::TYPE_TEXT,
            512,
            [],
            'Access Token'
        );

        $hubbox_auth->addColumn(
            'token_type',
            Table::TYPE_TEXT,
            10,
            [],
            'Token Type'
        );

        $hubbox_auth->addColumn(
            'refresh_token',
            Table::TYPE_TEXT,
            512,
            [],
            'Refresh Token'
        );

        $hubbox_auth->addColumn(
            'expires_in',
            Table::TYPE_TEXT,
            256,
            [],
            'Expires'
        );

        $hubbox_auth->addColumn(
            'scope',
            Table::TYPE_TEXT,
            128,
            [],
            'Scope'
        );

        $hubbox_auth->addColumn(
            'jti',
            Table::TYPE_TEXT,
            128,
            [],
            'Jti'
        );

        $hubbox_auth->addColumn(
            'timestamp',
            Table::TYPE_TIMESTAMP,
            256,
            [],
            'Timestamp'
        );

        $setup->getConnection()->createTable($hubbox_auth);

        $setup->endSetup();
    }
}
