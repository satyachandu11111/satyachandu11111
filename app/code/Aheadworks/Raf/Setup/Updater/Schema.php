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
namespace Aheadworks\Raf\Setup\Updater;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Schema
 * @package Aheadworks\Raf\Setup\Updater
 */
class Schema
{
    /**
     * Update to 1.0.1 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update101(SchemaSetupInterface $setup)
    {
        $this->changeTransactionTable($setup);

        return $this;
    }

    /**
     * Update to 1.1.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update110(SchemaSetupInterface $setup)
    {
        $this->addCumulativePercentToSummaryTable($setup);
        $this->addPercentBalanceAmountToTransactionTable($setup);
        $this->addStatusAndHoldingExprDateToTransactionTable($setup);

        return $this;
    }

    /**
     * Change transaction table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function changeTransactionTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('aw_raf_transaction');
        $connection = $installer->getConnection();

        if ($connection->tableColumnExists($tableName, 'created_at')) {
            $connection->changeColumn(
                $tableName,
                'created_at',
                'created_at',
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT,
                    'comment' => 'Created At'
                ]
            );
        }

        return $this;
    }

    /**
     * Add cumulative percent to summary table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addCumulativePercentToSummaryTable(SchemaSetupInterface $installer)
    {
        $tableName = 'aw_raf_summary';
        $this->addColumnsToTable(
            $installer,
            [
                [
                    'fieldName' => 'cumulative_percent_amount',
                    'config' => [
                        'type' => Table::TYPE_DECIMAL,
                        'nullable' => false,
                        'length' => '12,4',
                        'default' => '0.0000',
                        'after' => 'cumulative_amount',
                        'comment' => 'Cumulative Percent Amount'
                    ]
                ]
            ],
            $tableName
        );

        return $this;
    }

    /**
     * Add percent balance amount to transaction table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addPercentBalanceAmountToTransactionTable(SchemaSetupInterface $installer)
    {
        $tableName = 'aw_raf_transaction';
        $this->addColumnsToTable(
            $installer,
            [
                [
                    'fieldName' => 'percent_balance_amount',
                    'config' => [
                        'type' => Table::TYPE_DECIMAL,
                        'nullable' => false,
                        'length' => '12,4',
                        'default' => '0.0000',
                        'after' => 'balance_amount',
                        'comment' => 'Percent Balance Amount'
                    ]
                ]
            ],
            $tableName
        );

        return $this;
    }

    /**
     * Add status and holding period expiration fields to transaction table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addStatusAndHoldingExprDateToTransactionTable(SchemaSetupInterface $installer)
    {
        $tableName = 'aw_raf_transaction';
        $this->addColumnsToTable(
            $installer,
            [
                [
                    'fieldName' => 'status',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => 128,
                        'after' => 'percent_balance_amount',
                        'comment' => 'Status'
                    ]
                ]
            ],
            $tableName
        )->addColumnsToTable(
            $installer,
            [
                [
                    'fieldName' => 'holding_period_expiration',
                    'config' => [
                        'type' => Table::TYPE_TIMESTAMP,
                        'nullable' => true,
                        'default' => null,
                        'after' => 'status',
                        'comment' => 'Holding period expiration'
                    ]
                ]
            ],
            $tableName
        );

        return $this;
    }

    /**
     * Add columns to table
     *
     * @param SchemaSetupInterface $setup
     * @param array $columnsConfig
     * @param string $tableName
     * @return $this
     */
    private function addColumnsToTable($setup, $columnsConfig, $tableName)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable($tableName);
        foreach ($columnsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['fieldName'];
            if ($connection->tableColumnExists($tableName, $fieldName)) {
                continue;
            }
            $connection->addColumn(
                $tableName,
                $fieldName,
                $fieldConfig['config']
            );
        }

        return $this;
    }
}
