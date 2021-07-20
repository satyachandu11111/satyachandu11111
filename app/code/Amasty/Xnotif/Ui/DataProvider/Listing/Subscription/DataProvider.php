<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Ui\DataProvider\Listing\Subscription;

use Magento\Framework\Api\Filter;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var array
     */
    private $mappedFields = [
        'product_name' => 'IF(product_name_by_store.value IS NULL,'
            . 'product_name_default.value,product_name_by_store.value)',
        'product_sku' => 'product.sku',
        'last_name' => 'customer.lastname',
        'first_name' => 'customer.firstname',
        'store_name' => 'IF(customer.store_id IS NULL,store_name.store_id,customer.store_id)',
        'email' => 'IF(customer.email IS NULL,main_table.email,customer.email)',
        'store_id' => 'main_table.store_id'
    ];

    /**
     * @param Filter $filter
     * @return mixed|void
     */
    public function addFilter(Filter $filter)
    {
        if (array_key_exists($filter->getField(), $this->mappedFields)) {
            $filter->setField(new \Zend_Db_Expr($this->mappedFields[$filter->getField()]));
        }

        parent::addFilter($filter);
    }
}
