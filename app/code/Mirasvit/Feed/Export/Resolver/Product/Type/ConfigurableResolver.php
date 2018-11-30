<?php

namespace Mirasvit\Feed\Export\Resolver\Product\Type;

use Magento\Catalog\Model\Product;
use Mirasvit\Feed\Export\Resolver\ProductResolver;

class ConfigurableResolver extends ProductResolver
{
    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getAssociatedProducts($product)
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $type */
        $type = $product->getTypeInstance();

        return $type->getUsedProducts($product);
    }

    public function getData($object, $key)
    {
        $value = parent::getData($object, $key);

        # require huge amount of resources
        //        if (!$value) {
        //            $value = [];
        //
        //            foreach ($this->getAssociatedProducts($object) as $child) {
        //                $childValue = parent::getData($child, $key);
        //
        //                if (is_string($childValue)) {
        //                    $childValue = explode(', ', $childValue);
        //                }
        //
        //                if (is_array($childValue)) {
        //                    $value = array_merge($value, $childValue);
        //                }
        //            }
        //
        //            $value = array_unique(array_filter($value));
        //        }

        return $value;
    }
}
