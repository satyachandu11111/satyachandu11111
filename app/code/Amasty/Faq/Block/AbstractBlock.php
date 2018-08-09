<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class AbstractBlock extends Template
{
    /**
     * If module disabled then do not show output
     *
     * @return string
     */
    public function toHtml()
    {
        if (!$this->_scopeConfig->isSetFlag(ConfigProvider::ENABLED)) {
            return '';
        }

        return parent::toHtml();
    }
}
