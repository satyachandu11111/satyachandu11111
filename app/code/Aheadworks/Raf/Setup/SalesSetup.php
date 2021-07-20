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
namespace Aheadworks\Raf\Setup;

use Magento\Sales\Setup\SalesSetup as MagentoSalesSetup;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Raf\Api\Data\CreditmemoInterface;
use Aheadworks\Raf\Api\Data\CreditmemoItemInterface;
use Aheadworks\Raf\Api\Data\InvoiceInterface;
use Aheadworks\Raf\Api\Data\InvoiceItemInterface;
use Aheadworks\Raf\Api\Data\OrderInterface;
use Aheadworks\Raf\Api\Data\OrderItemInterface;

/**
 * Class SalesSetup
 *
 * @package Aheadworks\Raf\Setup
 */
class SalesSetup extends MagentoSalesSetup
{
    /**
     * Retrieve attributes config to install
     *
     * @return array
     */
    public function getAttributesToInstall()
    {
        $attributes = [
            [
                'attribute' => CreditmemoInterface::AW_RAF_AMOUNT,
                'type' => 'creditmemo',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CreditmemoInterface::BASE_AW_RAF_AMOUNT,
                'type' => 'creditmemo',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CreditmemoInterface::AW_RAF_IS_RETURN_TO_ACCOUNT,
                'type' => 'creditmemo',
                'params' => ['type' => Table::TYPE_SMALLINT]
            ],

            [
                'attribute' => CreditmemoItemInterface::AW_RAF_AMOUNT,
                'type' => 'creditmemo_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => CreditmemoItemInterface::BASE_AW_RAF_AMOUNT,
                'type' => 'creditmemo_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => InvoiceInterface::AW_RAF_AMOUNT,
                'type' => 'invoice',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => InvoiceInterface::BASE_AW_RAF_AMOUNT,
                'type' => 'invoice',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => InvoiceItemInterface::AW_RAF_AMOUNT,
                'type' => 'invoice_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => InvoiceItemInterface::BASE_AW_RAF_AMOUNT,
                'type' => 'invoice_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => OrderInterface::AW_RAF_REFERRAL_LINK,
                'type' => 'order',
                'params' => ['type' => 'varchar']
            ],
            [
                'attribute' => OrderInterface::AW_RAF_IS_FRIEND_DISCOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_SMALLINT]
            ],
            [
                'attribute' => OrderInterface::AW_RAF_IS_ADVOCATE_REWARD_RECEIVED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_SMALLINT]
            ],
            [
                'attribute' => OrderInterface::AW_RAF_AMOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_RAF_AMOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::AW_RAF_INVOICED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_RAF_INVOICED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::AW_RAF_REFUNDED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_RAF_REFUNDED,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::AW_RAF_SHIPPING_PERCENT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::AW_RAF_SHIPPING_AMOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderInterface::BASE_AW_RAF_SHIPPING_AMOUNT,
                'type' => 'order',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],

            [
                'attribute' => OrderItemInterface::AW_RAF_RULE_IDS,
                'type' => 'order_item',
                'params' => ['type' => 'varchar']
            ],
            [
                'attribute' => OrderItemInterface::AW_RAF_PERCENT,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::AW_RAF_AMOUNT,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::BASE_AW_RAF_AMOUNT,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::AW_RAF_INVOICED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::BASE_AW_RAF_INVOICED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::AW_RAF_REFUNDED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ],
            [
                'attribute' => OrderItemInterface::BASE_AW_RAF_REFUNDED,
                'type' => 'order_item',
                'params' => ['type' => Table::TYPE_DECIMAL]
            ]
        ];

        return $attributes;
    }

    /**
     * Remove entity attribute. Overwritten for flat entities support
     *
     * @param int|string $entityTypeId
     * @param string $code
     * @return $this
     */
    public function removeAttribute($entityTypeId, $code)
    {
        if (isset($this->_flatEntityTables[$entityTypeId])
            && $this->_flatTableExist($this->_flatEntityTables[$entityTypeId])
        ) {
            $this->removeFlatAttribute($this->_flatEntityTables[$entityTypeId], $code);
            $this->removeGridAttribute($this->_flatEntityTables[$entityTypeId], $code, $entityTypeId);
        } else {
            parent::removeAttribute($entityTypeId, $code);
        }

        return $this;
    }

    /**
     * Remove attribute as separate column in the table
     *
     * @param string $table
     * @param string $attribute
     * @return $this
     */
    protected function removeFlatAttribute($table, $attribute)
    {
        $tableInfo = $this->getConnection()->describeTable($this->getTable($table));
        if (isset($tableInfo[$attribute])) {
            $this->getConnection()->dropColumn($this->getTable($table), $attribute);
        }

        return $this;
    }

    /**
     * Remove attribute from grid table if necessary
     *
     * @param string $table
     * @param string $attribute
     * @param string $entityTypeId
     * @return $this
     */
    protected function removeGridAttribute($table, $attribute, $entityTypeId)
    {
        $table = $table . '_grid';
        $tableInfo = $this->getConnection()->describeTable($this->getTable($table));
        if (in_array($entityTypeId, $this->_flatEntitiesGrid) && isset($tableInfo[$attribute])) {
            $this->getConnection()->dropColumn($this->getTable($table), $attribute);
        }

        return $this;
    }
}
