<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;

class CollectVisits extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * CollectVisits constructor.
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $coreRegistry;
    }

    /**
     * @return string
     */
    public function getStatUrl()
    {
        return $this->_urlBuilder->getUrl('*/stat/collect');
    }

    /**
     * @return string
     */
    public function getStatData()
    {
        $categoryId = $this->registry->registry('current_faq_category_id');
        $questionId = $this->registry->registry('current_faq_question_id');
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $searchQuery = $this->getRequest()->getParam('query');
        return \Zend_Json::encode([
            'category_id' => $categoryId,
            'question_id' => $questionId,
            'page_url' => $currentUrl,
            'search_query' => $searchQuery,
            'ajax' => true
        ]);
    }
}
