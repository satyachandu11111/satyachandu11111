<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Controller\Price;

use Magento\Framework\App\RequestInterface;
use Magento\ProductAlert\Model\Observer;

class Index extends \Amasty\Xnotif\Controller\AbstractIndex
{
    const TYPE = "price";

    /**
     * @return bool
     */
    protected function isActive()
    {
        return $this->config->allowForCurrentCustomerGroup('price')
            && $this->productAlertHelper->isPriceAlertAllowed();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __("My Price Subscriptions");
    }
}
