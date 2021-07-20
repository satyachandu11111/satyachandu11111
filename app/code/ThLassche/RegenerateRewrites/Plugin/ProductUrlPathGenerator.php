<?php

namespace ThLassche\RegenerateRewrites\Plugin;


use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator as Subject;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class ProductUrlPathGenerator{

    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    public function aroundGetUrlPathWithSuffix(Subject $subject, callable $proceed, $product, $storeId, $category = NULL) {
        if ($category) {
            $category = $this->categoryRepository->get($category->getId(), $storeId);
            $category->setStoreId($storeId);
        }

        return $proceed($product, $storeId, $category);
    }
}
