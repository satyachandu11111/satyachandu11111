<?php

namespace Homescapes\CategoryFilter\Plugin;

class AddFilter
{
    public function aroundAddCategoryFilter(
   \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
   callable $proceed,
   \Magento\Catalog\Model\Category $category
    ) {
        //remember the category anchor flag
        $anchor = $category->getIsAnchor(); 
        //set anchor to 0.
        $category->setIsAnchor(0);
        //execute the original method and remember the result
        $result = $proceed($category);
        //set back the anchor flag on the category
        $category->setIsAnchor($anchor);
        //return what the original method returned;
        return $result;
    }
}