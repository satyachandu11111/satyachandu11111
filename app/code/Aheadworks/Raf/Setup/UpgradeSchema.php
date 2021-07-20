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

use Aheadworks\Raf\Setup\Updater\Schema;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Aheadworks\Raf\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
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
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updater->update101($setup);
        }
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->updater->update110($setup);
        }

        $setup->endSetup();
    }
}
