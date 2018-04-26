<?php
namespace Homescapes\Completelook\Model\Product\Type;

use \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;
use \Magento\GroupedProduct\Model\Product\Type\Grouped as TypeGrouped;


class Grouped
{
    public function afterGetAssociatedProductCollection(TypeGrouped $subject, Collection $result)
    {
        $result->addAttributeToSelect('size');               

        return $result;
    }
}