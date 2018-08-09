<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\View;

use Magento\Framework\View\Element\Template;

class Search extends Template
{
    /**
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getRequest()->getParam('query');
    }

    /**
     * Add metadata to page header
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($query = $this->getQuery()) {
            $title = __('Search results for "%1":', $this->escapeHtml($query));
            $this->pageConfig->getTitle()->set($title);

            /** @var \Magento\Theme\Block\Html\Title $headingBlock */
            if ($headingBlock = $this->getLayout()->getBlock('page.main.title')) {
                $headingBlock->setPageTitle($title);
            }
        }
        return parent::_prepareLayout();
    }
}
