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

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Raf\Api\Data\TotalsInterface;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Sales\Setup\SalesSetup;
use Aheadworks\Raf\Model\Source\Transaction\Status as TransactionStatus;

/**
 * Class Data
 *
 * @package Aheadworks\Raf\Setup\Updater
 */
class Data
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @param QuoteSetupFactory $setupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $setupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->quoteSetupFactory = $setupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * Update to 1.1.0 version
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    public function update110(ModuleDataSetupInterface $setup)
    {
        $this->addDiscountPercentAttribute($setup);
        $this->updateStatusInTransactionTable($setup);
        return $this;
    }

    /**
     * Add percent discount attribute to quote and order
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function addDiscountPercentAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute(
            'quote',
            TotalsInterface::AW_RAF_PERCENT_AMOUNT,
            ['type' => Table::TYPE_DECIMAL]
        );
        $quoteSetup->addAttribute(
            'quote',
            TotalsInterface::AW_RAF_AMOUNT_TYPE,
            ['type' => 'varchar']
        );

        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute(
            'order',
            TotalsInterface::AW_RAF_PERCENT_AMOUNT,
            ['type' => Table::TYPE_DECIMAL]
        );
        $salesSetup->addAttribute(
            'order',
            TotalsInterface::AW_RAF_AMOUNT_TYPE,
            ['type' => 'varchar']
        );
    }

    /**
     * Update status in transaction table
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function updateStatusInTransactionTable(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->update(
            $setup->getTable('aw_raf_transaction'),
            [
                TransactionInterface::STATUS => TransactionStatus::COMPLETE,
            ]
        );
    }
}
