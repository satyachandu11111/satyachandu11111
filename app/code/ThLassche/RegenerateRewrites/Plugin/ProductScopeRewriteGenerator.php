<?php
namespace ThLassche\RegenerateRewrites\Plugin;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductScopeRewriteGenerator as Subject;
class ProductScopeRewriteGenerator {
    public function beforeGenerateForSpecificStoreView(Subject $subject, $storeId, $productCategories, Product $product, $rootCategoryId = null)
    {
        if (is_array($productCategories))
        {
            foreach ($productCategories as $objCategory)
            {
                $objCategory->setStore($storeId);
            }
        }
        else
        {
            $productCategories->setStore($storeId);
        }
        return [$storeId, $productCategories, $product, $rootCategoryId];
    }
}