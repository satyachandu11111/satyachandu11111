<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Lists;

use Magento\Framework\View\Element\Template;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * Pager constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Rewrite getPageUrl to get correct URL with all rewrites since we doesn't use magento url_rewrite
     * Save only query parameter and add page number
     *
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        // Remove params
        $urlParts = explode('?', $this->_urlBuilder->getCurrentUrl());
        $currentUrl = $urlParts[0];
        if ($query = $this->getRequest()->getParam('query')) {
            $params['query'] = $query;
        }

        return $currentUrl . '?' . http_build_query($params);
    }
}
