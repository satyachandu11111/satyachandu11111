<?php

namespace Homescapes\RestrictProductShipToCountries\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	private $eavSetupFactory;

	public function __construct(EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}
	
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'restrict_product_ship_to_countries',
			[
				'label' => 'Restrict Ship To Countries',
				'type' => 'text',
				'input' => 'multiselect',
				'source' => 'Homescapes\RestrictProductShipToCountries\Model\Config\Product\RestrictProductShipToCountriesOption',
				'required' => false,
				'sort_order' => 30,
				'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
				'used_in_product_listing' => true,
				'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
				'visible_on_front' => false
			]
		);
	}
}