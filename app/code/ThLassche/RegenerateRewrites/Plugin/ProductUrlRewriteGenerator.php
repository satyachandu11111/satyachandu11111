<?php

namespace ThLassche\RegenerateRewrites\Plugin;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator as Subject;

class ProductUrlRewriteGenerator{

    private $productScopeRewriteGenerator;

    public function aroundGenerate(Subject $subject, callable $proceed, Product $product, $rootCategoryId = NULL) {
        $productCategories = $product->getCategoryCollection()
                                     ->addAttributeToSelect('url_key')
                                     ->addAttributeToSelect('url_path');

        $storeId = $product->getStoreId();

        if (is_array($productCategories)) {
            foreach ($productCategories as $objCategory) {
                $objCategory->setStore($storeId);
            }
        } else {
            $productCategories->setStore($storeId);
            foreach ($productCategories as $objCategory) {
                $objCategory->setStore($storeId);
            }
        }

        return $proceed($product, $rootCategoryId);
    }
}
