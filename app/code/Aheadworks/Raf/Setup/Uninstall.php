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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class Uninstall
 *
 * @package Aheadworks\Raf\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $dataSetup;

    /**
     * @param QuoteSetupFactory $setupFactory
     * @param ModuleDataSetupInterface $dataSetup
     */
    public function __construct(
        QuoteSetupFactory $setupFactory,
        ModuleDataSetupInterface $dataSetup
    ) {
        $this->quoteSetupFactory = $setupFactory;
        $this->dataSetup = $dataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->uninstallTables($installer)
            ->uninstallQuoteData()
            ->uninstallConfigData($installer);

        $installer->endSetup();
    }

    /**
     * Uninstall all module tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTables(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->dropTable('aw_raf_rule_website');
        $installer->getConnection()->dropTable('aw_raf_rule');
        $installer->getConnection()->dropTable('aw_raf_transaction_entity');
        $installer->getConnection()->dropTable('aw_raf_transaction');
        $installer->getConnection()->dropTable('aw_raf_summary');

        return $this;
    }

    /**
     * Uninstall quote data
     *
     * @return $this
     */
    private function uninstallQuoteData()
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $this->dataSetup]);
        $attributes = $quoteSetup->getAttributesToInstall();
        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->removeAttribute(
                $attributeParams['type'],
                $attributeParams['attribute']
            );
        }

        return $this;
    }

    /**
     * Uninstall module data from config
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallConfigData(SchemaSetupInterface $installer)
    {
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_raf/%'");

        return $this;
    }
}
