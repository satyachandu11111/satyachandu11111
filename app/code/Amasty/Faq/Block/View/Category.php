<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Api\Data\CategoryInterface;

class Category extends Template implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        \Magento\Framework\App\Http\Context $httpContext,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->httpContext = $httpContext;
        $this->setData('cache_lifetime', 86400);
        $this->configProvider = $configProvider;
    }

    /**
     * @return int
     */
    public function getShortAnswerBehavior()
    {
        return (int)$this->configProvider->getFaqPageShortAnswerBehavior();
    }

    /**
     * @return \Amasty\Faq\Api\Data\CategoryInterface
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_faq_category');
    }

    /**
     * @return int
     */
    public function getCurrentCategoryId()
    {
        return (int)$this->httpContext->getValue(\Amasty\Faq\Model\Context::CONTEXT_CATEGORY);
    }

    /**
     * Add metadata to page header
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var \Amasty\Faq\Api\Data\CategoryInterface $category */
        $category = $this->getCurrentCategory();
        if ($category) {
            $this->pageConfig->getTitle()->set($category->getMetaTitle() ? : $category->getTitle());
            if ($description = $category->getMetaDescription()) {
                $this->pageConfig->setDescription($description);
            }

            /** @var \Magento\Theme\Block\Html\Title $headingBlock */
            if ($headingBlock = $this->getLayout()->getBlock('page.main.title')) {
                $headingBlock->setPageTitle($category->getTitle());
            }

            if ($this->configProvider->isCanonicalUrlEnabled()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->getCanonicalUrl($category),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            if ($category->isNoindex() || $category->isNofollow()) {
                if ($category->isNoindex() && $category->isNofollow()) {
                    $this->pageConfig->setRobots('NOINDEX,NOFOLLOW');
                } elseif ($category->isNofollow()) {
                    $this->pageConfig->setRobots('NOFOLLOW');
                } else {
                    $this->pageConfig->setRobots('NOINDEX');
                }
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [\Amasty\Faq\Model\Category::CACHE_TAG . '_' . $this->getCurrentCategoryId()];
        /** @var \Amasty\Faq\Block\Lists\QuestionsList $listBlock */
        $listBlock = $this->getChildBlock('amasty_faq_questions');
        if ($listBlock) {
            $identities = array_merge($identities, $listBlock->getIdentities());
        }

        return $identities;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return parent::getCacheKeyInfo()
            + ['cat_id' => $this->getCurrentCategoryId()]
            + ['page' => (int)$this->getRequest()->getParam('p', 1)];
    }

    /**
     * Generate canonical url for page
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCanonicalUrl(CategoryInterface $category)
    {
        $urlKey = $this->configProvider->getUrlKey();
        return $this->_urlBuilder->getUrl($urlKey . '/' . $category->getCanonicalUrl());
    }

    /**
     * return FAQ Category Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getCurrentCategory()->getDescription();
    }
}
