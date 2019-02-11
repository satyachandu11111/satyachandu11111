<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

use Amasty\Sorting\Model\Source\Stock as StockSource;

/**
 * Class Instock
 * Method Using like additional sorting and not visible in the list of methods
 */
class Instock extends AbstractMethod
{
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory
     */
    private $stockStatusResourceFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory $stockStatusResourceFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        $connectionName = null,
        $methodCode = '',
        $methodName = ''
    ) {
        parent::__construct($context, $escaper, $connectionName, $methodCode, $methodName);
        $this->stockStatusResourceFactory = $stockStatusResourceFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction = '')
    {
        if (!$this->isMethodActive($collection)) {
            return $this;
        }

        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $qtyColumn = 'quantity';
            $salableColumn = 'is_salable';
        } else {
            $qtyColumn = 'qty';
            $salableColumn = 'stock_status';
        }

        /**
         * join in @see \Magento\CatalogInventory\Model\AddStockStatusToCollection
         * so we don't need to process join, only add sorting
         */
        if ($this->helper->getScopeValue('general/out_of_stock_qty')) {
            $ignoreTypes = [
                '\'configurable\'',
                '\'grouped\'',
                '\'bundle\''
            ];
            $collection->getSelect()->order(
                /** IF(stock_status_index.qty > 0, 0, 1) */
                $this->getConnection()->getCheckSql(
                    'stock_status_index.' . $qtyColumn . ' > ' . $this->helper->getQtyOutStock() . ' OR e.type_id in (' . implode(
                        ',',
                        $ignoreTypes
                    ) . ')',
                    '0',
                    '1'
                )
            );
        } else {
            $collection->getSelect()->order('stock_status_index.' . $salableColumn . ' ' . $collection::SORT_ORDER_DESC);
        }

        $orders = $collection->getSelect()->getPart(\Zend_Db_Select::ORDER);
        // move from the last to the the first position
        array_unshift($orders, array_pop($orders));
        $collection->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);

        return $this;
    }

    /**
     * Is can apply method sorting
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return bool
     */
    private function isMethodActive($collection)
    {
        if ($collection->getFlag('amasty_stock_sorted')) {
            return false;
        }

        // is out of stock is not displayed, method don't need to be applied
        $isShowOutOfStock = $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$isShowOutOfStock) {
            return false;
        }

        $show = $this->helper->getScopeValue('general/out_of_stock_last');

        if (!$show || ($show == StockSource::SHOW_LAST_FOR_CATALOG && $this->isSearchModule())) {
            return false;
        }

        return true;
    }

    /**
     * skip search results
     *
     * @return bool
     */
    private function isSearchModule()
    {
        return in_array(
            $this->request->getModuleName(),
            ['sqli_singlesearchresult', 'catalogsearch']
        );
    }
}
