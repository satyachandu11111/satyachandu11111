<?php
namespace Homescapes\MagazineImages\Setup;
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $eavSetup = $this->eavSetupFactory->create();

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'magazine_images',
                [
                    'type' => 'varchar',
                    'label' => 'Magazine Images',
                    'input' => 'media_image',
                    'required' => false,
                    'sort_order' => 30,
                    'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'user_defined' => true,
                    'visible' => true,
                    'visible_on_front' => true
                ]
            );
            $id = $eavSetup->getAttributeId(
                \Magento\Catalog\Model\Product::ENTITY,
                'magazine_images'
            );

           //$attributeSetId = $eavSetup->getDefaultAttributeSetId(\Magento\Catalog\Model\Product::ENTITY);
           $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
            foreach ($attributeSetIds as $attributeSetId) {
                $eavSetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, $attributeSetId, 'image-management', $id, 10);
            } 
        

        }

    }
}