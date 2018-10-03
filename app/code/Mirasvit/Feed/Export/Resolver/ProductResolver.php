<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.82
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Export\Resolver;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Relation as ProductRelation;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface as ProductMetadata;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Feed\Export\Context;
use Magento\Swatches\Helper\Data as SwatchesHelper;
use Mirasvit\Feed\Export\Resolver\Product\AbstractResolver as ProductAbstractResolver;
use Mirasvit\Feed\Model\Dynamic\Attribute;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductResolver extends AbstractResolver
{
    /**
     * Cache of loaded products
     *
     * @var array
     */
    private static $products = [];

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
     */
    private static $attributes;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var ProductRelation
     */
    private $productRelation;

    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var ProductMetadata
     */
    private $productMetadata;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var SwatchesHelper
     */
    private $swatchesHelper;

    /**
     * @var ProductAbstractResolver[]
     */
    private $resolvers;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        ProductRelation $productRelation,
        AttributeCollectionFactory $attributeCollectionFactory,
        ProductFactory $productFactory,
        ProductMetadata $productMetadata,
        ResourceConnection $resource,
        SwatchesHelper $swatchesHelper,
        Context $context,
        ObjectManagerInterface $objectManager,
        Attribute $dynamicAttribute,
        $resolvers = []
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->productRelation = $productRelation;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->productFactory = $productFactory;
        $this->productMetadata = $productMetadata;
        $this->resource = $resource;
        $this->swatchesHelper = $swatchesHelper;
        $this->dynamicAttribute = $dynamicAttribute;

        $this->resolvers = $resolvers;

        parent::__construct($context, $objectManager);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $result = [
            'entity_id'          => 'Product Id',
            'is_in_stock'        => 'Is In Stock',
            'qty'                => 'Qty',
            'image'              => 'Image',
            'url'                => 'Product Url',
            'category'           => 'Category Name',
            'category.entity_id' => 'Category Id',
            'category.path'      => 'Category Path (Category > Sub Category)',
            'gallery[0]'         => 'Image 2',
            'gallery[1]'         => 'Image 3',
            'gallery[2]'         => 'Image 4',
            'gallery[3]'         => 'Image 5',
            'attribute_set'      => 'Attribute Set',
            'type_id'            => 'Product Type',
            'price'              => 'Price',
            'regular_price'      => 'Regular Price',
            'special_price'      => 'Special Price',
            'final_price'        => 'Final Price',
            'tax_rate'           => 'Tax Rate',
        ];

        $entityTypeId = $this->objectManager->get('Magento\Eav\Model\Entity')
            ->setType(Product::ENTITY)->getTypeId();

        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('entity_type_id', $entityTypeId);

        foreach ($collection as $attribute) {
            if ($attribute->getStoreLabel()) {
                $code = $attribute->getAttributeCode();
                if (!isset($result[$code])) {
                    $result[$code] = $attribute->getStoreLabel() . ' [' . $code . ']';
                }
            }
        }

        $mappingCollectionFactory =
            $this->objectManager->create('Mirasvit\Feed\Model\ResourceModel\Dynamic\Category\CollectionFactory');

        /** @var \Mirasvit\Feed\Model\Dynamic\Category $mapping */
        foreach ($mappingCollectionFactory->create() as $mapping) {
            $label = $mapping->getName();
            $result['mapping:' . $mapping->getId()] = __('Category Mapping') . ': ' . $label;
        }

        $dynamicCollectionFactory =
            $this->objectManager->create('Mirasvit\Feed\Model\ResourceModel\Dynamic\Attribute\CollectionFactory');

        /** @var \Mirasvit\Feed\Model\Dynamic\Attribute $attribute */
        foreach ($dynamicCollectionFactory->create() as $attribute) {
            $label = $attribute->getName();
            $result['dynamic:' . $attribute->getCode()] = __('Dynamic Attribute') . ': ' . $label;
        }

        $dynamicVariableCollectionFactory =
            $this->objectManager->create('Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable\CollectionFactory');

        /** @var \Mirasvit\Feed\Model\Dynamic\Variable $variable */
        foreach ($dynamicVariableCollectionFactory->create() as $variable) {
            $label = $variable->getName();
            $result['variable:' . $variable->getCode()] = __('Dynamic Variable') . ': ' . $label;
        }

        return $result;
    }

    /**
     * Return full url for product
     *
     * @param Product $product
     *
     * @return string
     */
    public function getUrl($product)
    {
        $url = $product->getProductUrl();

        $getParams = [];

        $feed = $this->getFeed();

        if ($feed && $feed->getReportEnabled()) {
            $getParams['ff'] = $feed->getId();
            $getParams['fp'] = $product->getId();
        }

        $utmMap = [
            'utm_source'   => 'ga_source',
            'utm_medium'   => 'ga_medium',
            'utm_campaign' => 'ga_name',
            'utm_term'     => 'ga_term',
            'utm_content'  => 'ga_content',
        ];

        foreach ($utmMap as $key => $value) {
            if ($feed && $feed->getData($value)) {
                $getParams[$key] = $this->getFeed()->getData($value);
                if (preg_match('/{{product.*?}}/is', $getParams[$key])) {
                    $getParams[$key] = $this->dynamicAttribute->getLiquidValue(
                        $this,
                        $getParams[$key],
                        ['product' => $product]
                    );
                }
            }
        }

        if (count($getParams)) {
            $url .= strpos($url, '?') !== false ? '&' : '?';
            $url .= http_build_query($getParams);
        }

        return $url;
    }


    /**
     * Return product QTY
     *
     * @param Product $product
     * @return int
     */
    public function getQty($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        return $stockItem->getQty();
    }

    /**
     * Return product stock status
     *
     * @param Product $product
     * @return int
     */
    public function getIsInStock($product)
    {
        return $this->getQty($product) ? true : false;
    }

    /**
     * Attribute set name
     *
     * @param Product $product
     * @return string
     */
    public function getAttributeSet($product)
    {
        $attributeSetModel = $this->objectManager->create('\Magento\Eav\Model\Entity\Attribute\Set');
        $attributeSetModel->load($product->getAttributeSetId());

        return $attributeSetModel->getAttributeSetName();
    }

    /**
     * Parent product model or current product
     *
     * @param Product $product
     * @return Product
     */
    public function getParent($product)
    {
        $magentoEdition = $this->productMetadata->getEdition();
        $select = $this->productRelation->getConnection()->select()->from(
            $this->productRelation->getMainTable(),
            ['parent_id']
        )->where(
            'child_id = ?',
            $product->getId()
        );
        $parentIds = $this->productRelation->getConnection()->fetchCol($select);
        if (count($parentIds)) {
            if ($magentoEdition == 'Enterprise') {
                $parentRowId = $parentIds[0];
                $select = $this->productRelation->getConnection()->select()->from(
                    $this->resource->getTableName('catalog_product_entity'),
                    ['entity_id']
                )->where(
                    'row_id = ?',
                    $parentRowId
                );
                $parentIds = $this->productRelation->getConnection()->fetchCol($select);
            }
            return $this->productFactory->create()->load($parentIds[0]);
        } else {
            return $product;
        }
    }

    /**
     * Parent product model ONLY
     *
     * @param Product $product
     * @return Product | boolean
     */
    public function getOnlyParent($product)
    {
        $parent = $this->getParent($product);
        if ($parent && $parent->getId() != $product->getId()) {
            return $parent;
        }

        return false;
    }

    /**
     * For simple products
     *
     * @param Product $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAssociatedProducts($product)
    {
        return [];
    }

    /**
     * Related products
     *
     * @param Product $product
     * @return array
     */
    public function getRelatedProducts($product)
    {
        return $product->getRelatedProducts();
    }

    /**
     * CrossSell products
     *
     * @param Product $product
     * @return array
     */
    public function getCrossSellProducts($product)
    {
        return $product->getCrossSellProducts();
    }

    /**
     * UpSell products
     *
     * @param Product $product
     * @return array
     */
    public function getUpSellProducts($product)
    {
        return $product->getUpSellProducts();
    }

    /**
     * {@inheritdoc}
     */
    public function toString($value, $key = null)
    {
        if (!$key && $value instanceof Product) {
            return $value->getName();
        }

        return parent::toString($value, $key);
    }

    /**
     * Mapping model
     *
     * @param Product $product
     * @param [] $args
     * @return string
     */
    public function getMapping($product, $args)
    {
        $mappingId = $args[0];

        /** @var \Mirasvit\Feed\Model\Dynamic\Category $mapping */
        $mapping = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Category')->load($mappingId);

        $category = $this->getCategory($product);

        return $category ? $mapping->getMappingValue($category->getId()) : $mapping->getMappingValue(0);
    }

    /**
     * Dynamic attribute model
     *
     * @param Product $product
     * @param [] $args
     * @return string
     */
    public function getDynamic($product, $args)
    {
        $code = $args[0];

        /** @var \Mirasvit\Feed\Model\Dynamic\Attribute $attribute */
        $attribute = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Attribute')->load($code, 'code');

        if ($attribute) {
            return $attribute->getValue($product, $this);
        }

        return false;
    }

    /**
     * Dynamic variable model
     *
     * @param Product $product
     * @param [] $args
     * @return string
     */
    public function getVariable($product, $args)
    {
        $code = $args[0];

        /** @var \Mirasvit\Feed\Model\Dynamic\Variable $variable */
        $variable = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Variable')->load($code, 'code');

        if ($variable) {
            return $variable->getValue($product, $this);
        }

        return false;
    }

    /**
     * Collection of mappings
     *
     * @param Product $product
     * @param [] $args
     * @return array
     */
    public function getMappings($product, $args)
    {
        $mappingId = $args[0];

        /** @var \Mirasvit\Feed\Model\Dynamic\Category $mapping */
        $mapping = $this->objectManager->create('\Mirasvit\Feed\Model\Dynamic\Category')->load($mappingId);

        $result = [];
        foreach ($product->getCategoryCollection() as $category) {
            $result[] = $mapping->getMappingValue($category->getId());
        }

        return $result;
    }

    /**
     * @param Product $object
     * @param string $key
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getData($object, $key)
    {
        $result = false;

        foreach ($this->resolvers as $resolver) {
            $resolver->setFeed($this->getFeed());
            $result = $resolver->resolve($object, $key);
            if ($result !== false) {
                return $result;
            }
        }

        $product = $this->getProduct($object);

        $exploded = explode(':', $key);

        $key = $exploded[0];
        $modifier = count($exploded) == 2 ? $exploded[1] : "";

        $attribute = $this->getAttribute($key);

        if ($attribute && in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
            if (is_scalar($product->getData($key))) {

                if ($modifier == 'swatch') {
                    $value = $this->swatchesHelper->getSwatchesByOptionsId([$product->getData($key)]);
                    if ($value) {
                        $result = current($value)['value'] . '';
                    }
                } else {
                    $value = $product->getResource()
                        ->getAttribute($key)
                        ->getSource()
                        ->getOptionText($product->getData($key));

                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    $result = $value . '';
                }
            }
        } else {
            $result = $product->getDataUsingMethod($key);

            if (!$result) {
                $result = $product->getData($key);
            }
        }

        return $result;
    }

    /**
     * Return product attribute model by attribute code
     *
     * @param string $code
     * @return \Magento\Eav\Model\Entity\Attribute|null
     */
    protected function getAttribute($code)
    {
        if (self::$attributes == null) {
            $entityTypeId = $this->objectManager->get('Magento\Eav\Model\Entity')
                ->setType(Product::ENTITY)->getTypeId();

            self::$attributes = $this->objectManager
                ->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
                ->setEntityTypeFilter($entityTypeId);
        }

        $attribute = self::$attributes->getItemByColumnValue('attribute_code', $code);

        return $attribute;
    }

    /**
     * Load product model by object (from cache
     *
     * @param Product $object
     * @return Product
     */
    protected function getProduct($object)
    {
        if (!isset(self::$products[$object->getId()])) {
            self::$products[$object->getId()] = $object->load($object->getId());
        }
        return self::$products[$object->getId()];
    }

    /**
     * @param Product $object
     * @return Product
     */
    protected function prepareObject($object)
    {
        return $this->getProduct($object);
    }


    /**
     * Get category for product
     *
     * @param Product $product
     * @return Category
     */
    public function getCategory($product)
    {
        $collection = $product->getCategoryCollection();

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $collection->getFirstItem(); #default category

        #get category with maximum level
        $level = 0;
        $rootCategory = $this->getFeed() ? $this->getFeed()->getStore()->getRootCategoryId() : 0;
        /** @var \Magento\Catalog\Model\Category $cat */
        foreach ($collection as $cat) {
            if ($cat->getLevel() > $level && strpos($cat->getPath(), '/' . $rootCategory . '/') !== false) {
                $level = $cat->getLevel();
                $category = $cat;
            }
        }

        return $category;
    }

    /**
     * Get category collection for product (without root category)
     *
     * @param Product $product
     * @return array
     */
    public function getCategoryCollection($product)
    {
        $result = [];
        $collection = $product->getCategoryCollection();

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($collection as $category) {
            if ($category->isInRootCategoryList()) {
                $result[] = $category;
            }
        }

        return $result;
    }
}
