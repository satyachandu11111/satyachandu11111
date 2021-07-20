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

use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\ReminderStatus;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Raf\Model\Source\SubscriptionStatus;
use Aheadworks\Raf\Setup\Updater\Schema;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\Raf\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Schema
     */
    private $updater;

    /**
     * @param Schema $updater
     */
    public function __construct(
        Schema $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createRuleTable($installer)
            ->createRuleWebsiteTable($installer)
            ->createSummaryTable($installer)
            //->createSummaryMultipleAmountTable($installer) @todo M2RAF-24
            ->createTransactionTable($installer)
            ->createTransactionEntityTable($installer);

        $this->updater->update101($setup);
        $this->updater->update110($setup);
        $installer->endSetup();
    }

    /**
     * Create table 'aw_raf_rule'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_raf_rule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Rule ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )->addColumn(
                'registration_required',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Registration Required'
            )->addColumn(
                'friend_off',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Friend Off'
            )->addColumn(
                'friend_off_type',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Friend Off Type'
            )->addColumn(
                'advocate_off',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Advocate Off'
            )->addColumn(
                'advocate_off_type',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Advocate Off Type'
            )->addColumn(
                'advocate_earn_type',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Advocate Earn Type'
            )->addColumn(
                'apply_to_shipping',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Apply to Shipping'
            )->addIndex(
                $setup->getIdxName('aw_raf_rule', ['name']),
                ['name']
            )->addIndex(
                $setup->getIdxName('aw_raf_rule', ['status']),
                ['status']
            )->setComment('AW RAF Rule Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_raf_rule_website'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleWebsiteTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_raf_rule_website'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule ID'
            )->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )->addIndex(
                $setup->getIdxName('aw_raf_rule_website', ['rule_id']),
                ['rule_id']
            )->addIndex(
                $setup->getIdxName('aw_raf_rule_website', ['website_id']),
                ['website_id']
            )->addForeignKey(
                $setup->getFkName('aw_raf_rule_website', 'rule_id', 'aw_raf_rule', 'id'),
                'rule_id',
                $setup->getTable('aw_raf_rule'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_raf_rule_website', 'website_id', 'store_website', 'website_id'),
                'website_id',
                $setup->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )->setComment('AW RAF Rule To Website Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_raf_summary'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createSummaryTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_raf_summary'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Summary ID'
            )->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )->addColumn(
                'cumulative_amount',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Cumulative Amount'
            )->addColumn(
                'cumulative_amount_updated',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Cumulative Amount'
            )->addColumn(
                'invited_friends',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Invited Friends'
            )->addColumn(
                'expiration_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Expiration Date'
            )->addColumn(
                'new_reward_subscription_status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => SubscriptionStatus::NOT_SUBSCRIBED],
                'New Reward Subscription Status'
            )->addColumn(
                'referral_link',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Referral Link'
            )->addColumn(
                'reminder_status',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'default' => ReminderStatus::READY_TO_BE_SENT],
                'Reminder Status'
            )->addIndex(
                $setup->getIdxName('aw_raf_summary', ['customer_id']),
                ['customer_id']
            )->addIndex(
                $setup->getIdxName('aw_raf_summary', ['website_id']),
                ['website_id']
            )->addForeignKey(
                $setup->getFkName('aw_raf_summary', 'website_id', 'store_website', 'website_id'),
                'website_id',
                $setup->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_raf_summary', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('AW RAF Summary Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_raf_summary_multiple_amount'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createSummaryMultipleAmountTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_raf_summary_multiple_amount'))
            ->addColumn(
                'summary_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Summary ID'
            )->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                null,
                ['nullable' => false],
                'Amount'
            )->addColumn(
                'type',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Type'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $setup->getIdxName('aw_raf_summary_multiple_amount', ['summary_id']),
                ['summary_id']
            )->addForeignKey(
                $setup->getFkName('aw_raf_summary_multiple_amount', 'summary_id', 'aw_raf_summary', 'id'),
                'summary_id',
                $setup->getTable('aw_raf_summary_multiple_amount'),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW RAF Multiple Amount To Summary Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_raf_transaction'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTransactionTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_raf_transaction'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Transaction ID'
            )->addColumn(
                'summary_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Summary ID'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Created At'
            )->addColumn(
                'action',
                Table::TYPE_TEXT,
                150,
                ['unsigned' => true, 'nullable' => false],
                'Type'
            )->addColumn(
                'admin_comment',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Admin Comment'
            )->addColumn(
                'admin_comment_placeholder',
                Table::TYPE_TEXT,
                150,
                ['nullable' => true],
                'Admin Comment Placeholder'
            )->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Amount'
            )->addColumn(
                'amount_type',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Amount Type'
            )->addColumn(
                'balance_amount',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Current Balance Amount'
            )->addColumn(
                'created_by',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true],
                'Created By'
            )->addIndex(
                $setup->getIdxName('aw_raf_transaction', ['summary_id']),
                ['summary_id']
            )->addForeignKey(
                $setup->getFkName('aw_raf_transaction', 'summary_id', 'aw_raf_summary', 'id'),
                'summary_id',
                $setup->getTable('aw_raf_summary'),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW RAF Transaction Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_raf_transaction_entity'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createTransactionEntityTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('aw_raf_transaction_entity'))
            ->addColumn(
                'transaction_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Transaction ID'
            )->addColumn(
                'entity_type',
                Table::TYPE_TEXT,
                150,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Type'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'entity_label',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Entity Label'
            )->addIndex(
                $setup->getIdxName('aw_raf_transaction_entity', ['transaction_id', 'entity_type', 'entity_id']),
                ['transaction_id', 'entity_type', 'entity_id']
            )->addForeignKey(
                $setup->getFkName(
                    'aw_raf_transaction_entity',
                    'transaction_id',
                    'aw_raf_transaction',
                    'id'
                ),
                'transaction_id',
                $setup->getTable('aw_raf_transaction'),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW RAF Transaction Entity Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }
}
