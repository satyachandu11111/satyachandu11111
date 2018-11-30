<?php

namespace Mirasvit\Feed\Plugin;

use Magento\Store\Model\StoreManagerInterface;

class StoreResolverPlugin
{
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function afterGetCurrentStoreId($subject, $storeId)
    {
        if (isset($_SERVER['FEED_STORE_ID']) && $storeId !== $_SERVER['FEED_STORE_ID']) {
            return $_SERVER['FEED_STORE_ID'];
        }

        return $storeId;
    }
}