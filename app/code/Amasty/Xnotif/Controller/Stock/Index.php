<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Controller\Stock;

use Magento\Framework\App\RequestInterface;
use Magento\ProductAlert\Model\Observer;

class Index extends \Amasty\Xnotif\Controller\AbstractIndex
{
    const TYPE = "stock";

    /**
     * @return bool
     */
    protected function isActive()
    {
        return $this->productAlertHelper->isStockAlertAllowed();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("My Back in Stock Subscriptions");
    }
}
