<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionTemplates\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Product as ProductModel;

class Product extends ProductModel
{
    /**
     * @param array $productIds
     * @return array
     */
    public function getProductsByIds($productIds)
    {
        $select = $this->_resource->getConnection()->select();
        $select->from($this->_resource->getTableName('catalog_product_entity'))
               ->where('entity_id IN ('.implode(',', $productIds).')');
        return $this->_resource->getConnection()->fetchAll($select);
    }
}