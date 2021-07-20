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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Raf\Setup\Updater\Data;

/**
 * Class InstallData
 *
 * @package Aheadworks\Raf\Setup
 */
class InstallData implements InstallDataInterface
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
     * @var Data
     */
    private $updater;

    /**
     * @param QuoteSetupFactory $setupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param Data $updater
     */
    public function __construct(
        QuoteSetupFactory $setupFactory,
        SalesSetupFactory $salesSetupFactory,
        Data $updater
    ) {
        $this->quoteSetupFactory = $setupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->installSalesAttributes($setup)
            ->installQuoteAttributes($setup);
        $this->updater->update110($setup);
    }

    /**
     * Install sales attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function installSalesAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $attributes = $salesSetup->getAttributesToInstall();
        foreach ($attributes as $attributeParams) {
            $salesSetup->addAttribute(
                $attributeParams['type'],
                $attributeParams['attribute'],
                $attributeParams['params']
            );
        }

        return $this;
    }

    /**
     * Install quote attributes
     *
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    private function installQuoteAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $attributes = $quoteSetup->getAttributesToInstall();
        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute(
                $attributeParams['type'],
                $attributeParams['attribute'],
                $attributeParams['params']
            );
        }

        return $this;
    }
}
