<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */

/**
 * Helper class to detect question category id
 */
namespace Amasty\Faq\Model;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Session\Generic as Session;

class ResolveQuestionCategory
{
    /**
     * @var Session
     */
    private $faqSession;

    /**
     * @var ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Session $faqSession,
        CollectionFactory\Proxy $categoryCollectionFactory
    ) {
        $this->faqSession = $faqSession;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param QuestionInterface $question
     *
     * @return int|null
     */
    public function execute(QuestionInterface $question)
    {
        $categoryId = 0;
        $categories = $question->getCategories();
        if (!empty($categories)) {
            if (false !== strpos($categories, ',')) {
                $categoryIds = explode(',', $categories);
                $categoryId = $this->faqSession->getLastVisitedFaqCategoryId();
                if ($categoryId && in_array($categoryId, $categoryIds)) {
                    return $categoryId;
                }
                /** @var \Amasty\Faq\Model\ResourceModel\Category\Collection $collection */
                $collection = $this->categoryCollectionFactory->create();
                $collection->addFrontendFilters($this->storeManager->getStore()->getId())
                    ->addFieldToFilter('main_table.' . CategoryInterface::CATEGORY_ID, ['in' => $categoryIds])
                    ->setPageSize(1)
                    ->setCurPage(1);

                return $collection->getFirstItem()->getCategoryId();
            }
            $categoryId = $categories;
        }

        return (int)$categoryId;
    }
}
