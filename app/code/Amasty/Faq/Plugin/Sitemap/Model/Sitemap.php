<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Plugin\Sitemap\Model;

use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Amasty\Faq\Model\ResourceModel\Question\SitemapCollection as QuestionCollection;
use Amasty\Faq\Model\ResourceModel\Category\SitemapCollection as CategoryCollection;
use Amasty\Faq\Model\ConfigProvider;
use Magento\Sitemap\Model\Sitemap as BaseSitemap;

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
     * @param BaseSitemap $object
     */
    public function afterCollectSitemapItems(BaseSitemap $object)
    {
        $storeId = $object->getStoreId();
        if (!$this->configProvider->isSiteMapEnabled(ScopeInterface::SCOPE_STORE, $storeId)) {
            return;
        }

        $frequency = $this->configProvider->getFrequency(ScopeInterface::SCOPE_STORE, $storeId);
        $priority = $this->configProvider->getSitemapPriority(ScopeInterface::SCOPE_STORE, $storeId);

        $object->addSitemapItem(
            new DataObject(
                [
                    'changefreq' => $frequency,
                    'priority' => $priority,
                    'collection' => $this->questionCollection->getCollection($storeId),
                ]
            )
        );

        $object->addSitemapItem(
            new DataObject(
                [
                    'changefreq' => $frequency,
                    'priority' => $priority,
                    'collection' => $this->categoryCollection->getCollection($storeId),
                ]
            )
        );
    }
}
