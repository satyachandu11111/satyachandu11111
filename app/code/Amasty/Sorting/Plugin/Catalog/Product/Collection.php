<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Catalog\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;

/**
 * Plugin Collection
 * plugin name: Amasty_Sorting::SortingMethodsProcessor
 * type: \Magento\Catalog\Model\ResourceModel\Product\Collection
 */
class Collection
{
    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * @var \Amasty\Sorting\Model\SortingAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Amasty\Sorting\Model\ResourceModel\Method\Image
     */
    private $imageMethod;

    /**
     * @var \Amasty\Sorting\Model\ResourceModel\Method\Instock
     */
    private $stockMethod;

    /**
     * @var \Amasty\Sorting\Model\Logger
     */
    private $logger;

    public function __construct(
        \Amasty\Sorting\Helper\Data $helper,
        \Amasty\Sorting\Model\MethodProvider $methodProvider,
        \Amasty\Sorting\Model\ResourceModel\Method\Image $imageMethod,
        \Amasty\Sorting\Model\ResourceModel\Method\Instock $stockMethod,
        \Amasty\Sorting\Model\SortingAdapterFactory $adapterFactory,
        \Amasty\Sorting\Model\Logger $logger
    ) {
        $this->helper         = $helper;
        $this->methodProvider = $methodProvider;
        $this->adapterFactory = $adapterFactory;
        $this->imageMethod    = $imageMethod;
        $this->stockMethod    = $stockMethod;
        $this->logger         = $logger;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param string                                                  $attribute
     * @param string                                                  $dir  'ASC'|'DESC'
     *
     * @return array
     */
    public function beforeSetOrder($subject, $attribute, $dir = Select::SQL_DESC)
    {
        $this->applyHighPriorityOrders($subject, $dir);
        $method = $this->methodProvider->getMethodByCode($attribute);
        if ($method && !$subject->getFlag($this->getFlagName($attribute))) {
            $subject->setFlag($this->getFlagName($attribute), true);
            $method->apply($subject, $dir);
            $attribute = $method->getAlias();
            $this->logger->logCollectionQuery($subject);
        } elseif ($attribute == 'price') {
            $subject->addOrder($attribute, $dir);
            $attribute = 'am_price_sorting';
        } elseif ($attribute == 'relevance' && !$subject->getFlag($this->getFlagName('am_relevance'))) {
            $this->addRelevanceSorting($subject, $dir);
            $attribute = 'am_relevance';
        }

        return [$attribute, $dir];
    }

    private function getFlagName($attribute)
    {
        if (is_string($attribute)) {
            return 'sorted_by_' . $attribute;
        }

        return 'amasty_sorting';
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string $dir
     */
    private function applyHighPriorityOrders($collection, $dir)
    {
        if (!$collection->getFlag($this->getFlagName('high'))) {
            $this->stockMethod->apply($collection, $dir);
            $this->imageMethod->apply($collection, $dir);
            $collection->setFlag($this->getFlagName('high'), true);
        }
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    private function addRelevanceSorting($collection)
    {
        $collection->getSelect()->columns(['am_relevance' => new \Zend_Db_Expr(
            'search_result.'. TemporaryStorage::FIELD_SCORE
        )]);
        $collection->addExpressionAttributeToSelect('am_relevance', 'am_relevance', []);

        // remove last item from columns because e.am_relevance from addExpressionAttributeToSelect not exist
        $columns = $collection->getSelect()->getPart(\Zend_Db_Select::COLUMNS);
        array_pop($columns);
        $collection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, $columns);
        $collection->setFlag($this->getFlagName('am_relevance'), true);
    }
}
