<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


declare(strict_types=1);

namespace Amasty\Xnotif\Model\ResourceModel\Unsubscribe;

class AlertProvider
{
    const STOCK_TYPE = 'stock';

    const PRICE_TYPE = 'price';

    const REMOVE_ALL = 'all';

    /**
     * @var array
     */
    private $alertFactory = [];

    public function __construct(
        \Magento\ProductAlert\Model\PriceFactory $priceFactory,
        \Magento\ProductAlert\Model\StockFactory $stockFactory
    ) {
        $this->alertFactory[self::PRICE_TYPE] = $priceFactory;
        $this->alertFactory[self::STOCK_TYPE] = $stockFactory;
    }

    /**
     * @param string $type
     * @param string|int $productId
     * @param array $subscribeConditions
     * @return null
     */
    public function getAlertModel(string $type, $productId, array $subscribeConditions)
    {
        $collection = null;
        $type = str_replace(self::REMOVE_ALL, '', strtolower($type));

        if (isset($this->alertFactory[$type])) {
            $collection = $this->alertFactory[strtolower($type)]->create()->getCollection();
        }
        if (empty($collection)) {
            return null;
        }

        if (strcmp($productId, self::REMOVE_ALL) != 0) {
            $collection->addFieldToFilter('parent_id', $productId);
        }
        foreach ($subscribeConditions as $field => $value) {
            $collection->addFieldToFilter($field, $value);
        }

        return $collection;
    }
}
