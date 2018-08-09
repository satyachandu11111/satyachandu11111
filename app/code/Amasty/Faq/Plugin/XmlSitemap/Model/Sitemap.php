<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Plugin\XmlSitemap\Model;

use Magento\Store\Model\ScopeInterface;
use Amasty\Faq\Model\ResourceModel\Question\SitemapCollection as QuestionCollection;
use Amasty\Faq\Model\ResourceModel\Category\SitemapCollection as CategoryCollection;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\XmlSitemap\Model\Sitemap as XmlSitemap;

class Sitemap
{
    /**
     * @var QuestionCollection
     */
    private $questionCollection;
    /**
     * @var CategoryCollection
     */
    private $categoryCollection;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Sitemap constructor.
     * @param QuestionCollection $questionCollection
     * @param CategoryCollection $categoryCollection
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        QuestionCollection $questionCollection,
        CategoryCollection $categoryCollection,
        ConfigProvider $configProvider
    ) {
        $this->questionCollection = $questionCollection;
        $this->categoryCollection = $categoryCollection;
        $this->configProvider = $configProvider;
    }

    /**
     * @param XmlSitemap $subject
     * @param \Closure $proceed
     * @param $storeId
     * @return array|bool
     */
    public function aroundGetFaqCategoriesPageCollection(XmlSitemap $subject, \Closure $proceed, $storeId)
    {
        $collection = [];
        if ($this->configProvider->isSiteMapEnabled(ScopeInterface::SCOPE_STORE, $storeId)) {
            $collection = $this->categoryCollection->getCollection($storeId);
        }

        return $collection;
    }

    /**
     * @param XmlSitemap $subject
     * @param \Closure $proceed
     * @param $storeId
     * @return array|bool
     */
    public function aroundGetFaqQuestionsPageCollection(XmlSitemap $subject, \Closure $proceed, $storeId)
    {
        $collection = [];
        if ($this->configProvider->isSiteMapEnabled(ScopeInterface::SCOPE_STORE, $storeId)) {
            $collection = $this->questionCollection->getCollection($storeId);
        }

        return $collection;
    }
}
