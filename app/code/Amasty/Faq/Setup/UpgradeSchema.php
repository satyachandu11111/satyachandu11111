<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeTo110
     */
    private $upgradeTo110;

    /**
     * @var Operation\UpgradeTo200
     */
    private $upgradeTo200;

    /**
     * @param SchemaSetupInterface   $setup
     * @var Operation\CreateViewStatTables
     */
    private $createStatTable;

    /**
     * @var Operation\AddTriggers
     */
    private $triggers;

    /**
     * UpgradeSchema constructor.
     * @param Operation\UpgradeTo110 $upgradeTo110
     * @param Operation\UpgradeTo200 $upgradeTo200
     * @param Operation\CreateViewStatTables $createStatTable
     * @param Operation\AddTriggers $triggers
     */
    public function __construct(
        Operation\UpgradeTo110 $upgradeTo110,
        Operation\UpgradeTo200 $upgradeTo200,
        Operation\CreateViewStatTables $createStatTable,
        Operation\AddTriggers $triggers
    ) {
        $this->upgradeTo110 = $upgradeTo110;
        $this->upgradeTo200 = $upgradeTo200;
        $this->createStatTable = $createStatTable;
        $this->triggers = $triggers;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->createStatTable->execute($setup);
            $this->upgradeTo110->execute($setup);
            $this->triggers->addVisitStatTrigger($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->upgradeTo200->execute($setup);
        }

        $setup->endSetup();
    }
}
