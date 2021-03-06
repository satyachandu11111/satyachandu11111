<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use MageWorx\ShippingRules\Model\Region as RegionModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\EntityManager\MetadataPool;
use MageWorx\ShippingRules\Api\Data\RuleInterface;
use MageWorx\ShippingRules\Model\Rule;
use MageWorx\ShippingRules\Api\Data\ZoneInterface;
use MageWorx\ShippingRules\Model\Zone;
use MageWorx\ShippingRules\Model\Carrier;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Framework\DB\AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param MetadataPool $metadataPool
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        MetadataPool $metadataPool,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->productMetadata = $productMetadata;
        $this->metadataPool    = $metadataPool;
        if ($this->isUsedJsonSerializedValues()) {
            $this->aggregatedFieldConverter = $objectManager->get('Magento\Framework\DB\AggregatedFieldDataConverter');
            $this->jsonSerializer           = $objectManager->get('Magento\Framework\Serialize\Serializer\Json');
        }
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return bool
     */
    public function isUsedJsonSerializedValues()
    {
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.2.0', '>=') &&
            class_exists('\Magento\Framework\DB\AggregatedFieldDataConverter')
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->addDefaultValuesForDefaultRegions($setup);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<') && $this->aggregatedFieldConverter) {
            $this->convertRuleSerializedDataToJson($setup);
            $this->convertZoneSerializedDataToJson($setup);
        }

        if (version_compare($context->getVersion(), '2.0.7', '<')) {
            $this->createRatesCodesFromIds($setup);
        }

        if (version_compare($context->getVersion(), '2.4.0', '<')) {
            $this->addShippingPerProductAttributes();
        }

        $setup->endSetup();
    }

    /**
     * Add default values for the default regions:
     * is_active = 1
     * is_custom = 0
     *
     * @param ModuleDataSetupInterface $setup
     */
    protected function addDefaultValuesForDefaultRegions(ModuleDataSetupInterface $setup)
    {
        $connection           = $setup->getConnection();
        $regionsTable         = $setup->getTable('directory_country_region');
        $extendedRegionsTable = $setup->getTable(RegionModel::EXTENDED_REGIONS_TABLE_NAME);
        $select               = $connection->select()->from($regionsTable, ['region_id']);
        $query                = $connection->insertFromSelect(
            $select,
            $extendedRegionsTable,
            ['region_id'],
            AdapterInterface::INSERT_IGNORE
        );
        $connection->query($query);
        $connection->update($extendedRegionsTable, ['is_active' => 1, 'is_custom' => 0]);
    }

    /**
     * Convert Rule metadata from serialized to JSON format:
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     * @throws \Magento\Framework\DB\FieldDataConversionException
     */
    protected function convertRuleSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        $metadata = $this->metadataPool->getMetadata(RuleInterface::class);
        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'conditions_serialized'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'actions_serialized'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'amount'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'action_type'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'shipping_methods'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'disabled_shipping_methods'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'enabled_shipping_methods'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Rule::TABLE_NAME),
                    $metadata->getLinkField(),
                    'store_errmsgs'
                ),
            ],
            $setup->getConnection()
        );
    }

    /**
     * Convert Zone metadata from serialized to JSON format:
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     * @throws \Magento\Framework\DB\FieldDataConversionException
     */
    protected function convertZoneSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        $metadata = $this->metadataPool->getMetadata(ZoneInterface::class);
        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable(Zone::ZONE_TABLE_NAME),
                    $metadata->getLinkField(),
                    'conditions_serialized'
                ),
            ],
            $setup->getConnection()
        );
    }

    /**
     * Add default codes for the existing rates from its id with prefix
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function createRatesCodesFromIds(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $ratesTable = $setup->getTable(Carrier::RATE_TABLE_NAME);
        $connection->update(
            $ratesTable,
            ['rate_code' => new \Zend_Db_Expr("CONCAT('rate_',`rate_id`)")],
            ["`rate_code` IS NULL OR `rate_code` = ''"]
        );
    }

    /**
     * Adds available_shipping_methods attribute to the product EAV-model
     */
    private function addShippingPerProductAttributes()
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        $availableShippingMethodsAttribute = $eavSetup->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'available_shipping_methods'
        );
        if (empty($availableShippingMethodsAttribute)) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'available_shipping_methods',
                [
                    'group'                    => 'General',
                    'type'                     => 'text',
                    'label'                    => 'Available Shipping Methods',
                    'input'                    => 'multiselect',
                    'required'                 => false,
                    'sort_order'               => 40,
                    'global'                   => ScopedAttributeInterface::SCOPE_STORE,
                    'is_used_in_grid'          => true,
                    'is_visible_in_grid'       => true,
                    'is_filterable_in_grid'    => true,
                    'visible'                  => true,
                    'is_html_allowed_on_front' => false,
                    'visible_on_front'         => false,
                    'system'                   => 0,
                    // should be 0 to access this attribute everywhere
                    'user_defined'             => false,
                    // should be false to prevent deleting from admin-side interface
                    'source'                   =>
                        \MageWorx\ShippingRules\Model\Attribute\Source\AvailableShippingMethods::class,
                    'frontend'                 =>
                        \MageWorx\ShippingRules\Model\Attribute\Frontend\AvailableShippingMethods::class,
                    'backend'                  =>
                        \MageWorx\ShippingRules\Model\Attribute\Backend\AvailableShippingMethods::class,
                    // Extends Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend
                ]
            );
        }
    }
}
