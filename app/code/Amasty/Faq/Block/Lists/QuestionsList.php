<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Lists;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\Question;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory;
use Amasty\Faq\Model\ResourceModel\Question\Collection;

class QuestionsList extends \Amasty\Faq\Block\AbstractBlock implements IdentityInterface
{
    const CATEGORY_PAGE = 1;
    const PRODUCT_PAGE = 2;
    const SEARCH_PAGE = 3;
    /**
     * Core registry
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Question collection
     *
     * @var Collection
     */
    private $collection;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Context
     */
    private $httpContext;

    /**
     * @var array
     */
    private $toHighlight = [];

    /**
     * @var bool
     */
    private $withRating;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $pageType;

    /**
     * QuestionsList constructor.
     *
     * @param Template\Context  $context
     * @param Registry          $coreRegistry
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider    $configProvider
     * @param Context           $httpContext
     * @param array             $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->configProvider = $configProvider;
        $this->httpContext = $httpContext;
        $this->withRating = isset($data['with_rating']) && $data['with_rating'] ? true : false;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function highlight($text)
    {
        if ($this->toHighlight) {

            return preg_replace_callback('/(' . implode('|', $this->toHighlight) . ')/isu', function ($match) {
                return '<span class="amfaq-highlight">' . $match[0] . '</span>';
            }, $text);
        }
        return $text;
    }

    /**
     * @param Question $question
     *
     * @return string
     */
    public function getShortAnswer(Question $question)
    {
        return $question->prepareShortAnswer(
            $this->configProvider->getLimitShortAnswer(),
            $this->getParentBlock()->getShortAnswerBehavior()
        );
    }

    /**
     * @return Question[]
     */
    private function questionItems()
    {
        return $this->collection->getItems();
    }

    /**
     * Get questions collection
     *
     * @return Collection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            if ($categoryId = $this->getCategoryId()) {
                $this->generateQuestionsForCategory($categoryId);
                $this->pageType = self::CATEGORY_PAGE;
            } elseif ($productId = $this->getProductId()) {
                $this->generateQuestionsForProduct($productId);
                $this->pageType = self::PRODUCT_PAGE;
            } else {
                $this->generateSearchResult();
                $this->pageType = self::SEARCH_PAGE;
            }

            if ($this->getLimit()) {
                $curPage = (int)$this->getRequest()->getParam('p', 1);
                $this->collection->setCurPage($curPage);
                $this->collection->setPageSize($this->getLimit());
            }

            $this->collection->addFrontendFilters(
                (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH),
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->collection;
    }

    /**
     * Generate collection for product page
     *
     * @param $productId
     */
    protected function generateQuestionsForProduct($productId)
    {
        $this->collection->addProductFilter($productId);
    }

    /**
     * Generate collection for category page
     *
     * @param $categoryId
     */
    protected function generateQuestionsForCategory($categoryId)
    {
        $this->collection->addCategoryFilter($categoryId);
    }

    /**
     * @return Question[]
     */
    public function getQuestions()
    {
        $this->getCollection();
        $questions = $this->questionItems();

        return $questions;
    }

    /**
     * Generate collection for search page
     */
    protected function generateSearchResult()
    {
        /** @var \Amasty\Faq\Block\View\Search $searchBlock */
        $searchBlock = $this->getParentBlock();
        if ($searchBlock && $searchBlock->getQuery()) {
            $this->toHighlight = $this->collection->loadByQueryText($searchBlock->getQuery());
        }
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        if ($product = $this->coreRegistry->registry('current_product')) {
            return (int)$product->getId();
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getCategoryId()
    {
        if ($category = $this->coreRegistry->registry('current_faq_category')) {
            return (int)$category->getId();
        }

        return null;
    }

    /**
     * Generate link to Question page
     *
     * @param QuestionInterface $question
     *
     * @return string
     */
    public function getQuestionLink(QuestionInterface $question)
    {
        return $this->_urlBuilder->getUrl($this->configProvider->getUrlKey() . '/' . $question->getUrlKey());
    }

    /**
     * @return string
     */
    public function getNoItemsLabel()
    {
        if ($this->getParentBlock()->getNameInLayout() === 'amasty_faq_search_view') {

            return (string)$this->configProvider->getNoItemsLabel();
        }

        return __('No Questions');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Collection::CACHE_TAG];
    }

    /**
     * Return Pager html for all pages
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('amasty_faq_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject && $this->isPaginationEnabled()) {

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        }

        return '';
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        if ($this->limit === null) {
            switch ($this->pageType) {
                case self::CATEGORY_PAGE:
                    $this->limit = $this->configProvider->getCategoryPageSize();
                    break;
                case self::PRODUCT_PAGE:
                    $this->limit = $this->configProvider->getProductPageSize();
                    break;
                case self::SEARCH_PAGE:
                    $this->limit = $this->configProvider->getSearchPageSize();
                    break;
                default:
                    $this->limit = false;
            }
        }

        return $this->limit;
    }

    /**
     * @return bool
     */
    public function isPaginationEnabled()
    {
        if ($this->pageType == self::PRODUCT_PAGE) {
            return false;
        }

        return (bool)$this->getLimit();
    }
}