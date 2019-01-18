<?php

namespace Dividebuy\RetailerConfig\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'dividebuy_enable', [
                'type'             => 'int',
                'group'            => 'DivideBuy',
                'backend'          => '',
                'frontend'         => '',
                'label'            => 'DivideBuy Enabled',
                'input'            => 'select',
                'class'            => '',
                'source'           => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global'           => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible'          => true,
                'required'         => false,
                'user_defined'     => true,
                'default'          => 1,
                'searchable'       => false,
                'filterable'       => false,
                'comparable'       => false,
                'visible_on_front' => false,
                'unique'           => false,
                'apply_to'         => '',
            ]
        );

        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'dividebuy_tax_class',
            [
                'type'             => 'int',
                'group'            => 'DivideBuy',
                'backend'          => '',
                'frontend'         => '',
                'label'            => 'DivideBuy Tax Class',
                'input'            => 'select',
                'class'            => '',
                'source'           => '',
                'global'           => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible'          => true,
                'required'         => false,
                'user_defined'     => true,
                'searchable'       => false,
                'filterable'       => false,
                'comparable'       => false,
                'visible_on_front' => false,
                'unique'           => false,
                'apply_to'         => '',
                'option'           => array(
                    'values' => array(
                        20 => 'VAT Standard - 20%',
                        7  => 'VAT Reduced - 7%',
                        5  => 'VAT Reduced - 5%',
                        0  => 'VAT Zero - 0%',
                    ),
                ),
            ]
        );
    }
}
