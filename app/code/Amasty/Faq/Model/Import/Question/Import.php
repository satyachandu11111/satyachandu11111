<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Import\Question;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Api\ImportExport\QuestionInterface;
use Amasty\Faq\Model\Import\AbstractImport;
use Amasty\Faq\Model\Import\ImportValidatorInterface;
use Amasty\Faq\Model\OptionSource\Question\Status;
use Amasty\Faq\Model\OptionSource\Question\Visibility;
use Amasty\Faq\Model\ResourceModel\Question\InsertDummyQuestion;
use Amasty\Faq\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Amasty\Faq\Model\QuestionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Import extends AbstractImport
{
    const ENTITY_TYPE_CODE = 'faq_question_import';

    /**
     * @var array
     */
    protected $validColumnNames = [
        QuestionInterface::QUESTION_ID,
        QuestionInterface::QUESTION,
        QuestionInterface::URL_KEY,
        QuestionInterface::STORE_CODES,
        QuestionInterface::SHORT_ANSWER,
        QuestionInterface::ANSWER,
        QuestionInterface::STATUS,
        QuestionInterface::VISIBILITY,
        QuestionInterface::POSITION,
        QuestionInterface::META_TITLE,
        QuestionInterface::META_DESCRIPTION,
        QuestionInterface::NAME,
        QuestionInterface::EMAIL,
        QuestionInterface::CATEGORY_IDS,
        QuestionInterface::PRODUCT_SKUS,
    ];

    protected $masterAttributeCode = QuestionInterface::QUESTION;

    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    /**
     * @var array
     */
    private $stores = [];

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var InsertDummyQuestion
     */
    private $dummyQuestion;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        QuestionRepositoryInterface $repository,
        QuestionFactory $questionFactory,
        CollectionFactory $categoryCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        InsertDummyQuestion $dummyQuestion,
        StoreManagerInterface $storeManager,
        ImportValidatorInterface $importValidator,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct(
            $importValidator,
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $errorAggregator,
            $resource,
            $data
        );
        $this->repository = $repository;
        $this->questionFactory = $questionFactory;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->resource = $resource;
        $this->dummyQuestion = $dummyQuestion;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param array $data
     */
    protected function customBehaviorData(array $data)
    {
        $this->setStores();
        foreach ($data as $questionData) {
            $question = $this->questionFactory->create();
            $this->setQuestionData($question, $questionData);
            try {
                $this->repository->save($question);
            } catch (CouldNotSaveException $e) {

            }
        }
    }

    /**
     * @param array $data
     */
    protected function addUpdateBehaviorData(array $data)
    {
        $this->setStores();
        $questionsToCreate = [];
        foreach ($data as $questionData) {
            $question = null;
            $questionData[QuestionInterface::QUESTION_ID] = (int)$questionData[QuestionInterface::QUESTION_ID];
            if (!empty($questionData[QuestionInterface::QUESTION_ID])) {
                try {
                    $question = $this->repository->getById($questionData[QuestionInterface::QUESTION_ID]);
                } catch (NoSuchEntityException $e) {
                    $dummyQuestion = $this->questionFactory->create();
                    $dummyQuestion->setQuestionId($questionData[QuestionInterface::QUESTION_ID]);
                    $this->dummyQuestion->save($dummyQuestion);
                    try {
                        $question = $this->repository->getById($questionData[QuestionInterface::QUESTION_ID]);
                    } catch (NoSuchEntityException $e) {

                    }
                }

                if ($question) {
                    $this->setQuestionData($question, $questionData);
                    try {
                        $this->repository->save($question);
                    } catch (CouldNotSaveException $e) {

                    }
                }
            } else {
                $questionsToCreate[] = $questionData;
            }
        }

        if (!empty($questionsToCreate)) {
            $this->customBehaviorData($questionsToCreate);
        }
    }

    /**
     * @param array $data
     */
    protected function deleteBehaviorData(array $data)
    {
        foreach ($data as $questionData) {
            if (!empty($questionData[QuestionInterface::QUESTION_ID])) {
                try {
                    $this->repository->deleteById((int)$questionData[QuestionInterface::QUESTION_ID]);
                } catch (CouldNotDeleteException $e) {

                }
            }
        }
    }

    /**
     * @param \Amasty\Faq\Model\Question $question
     * @param array                      $questionData
     */
    private function setQuestionData(\Amasty\Faq\Model\Question $question, $questionData = [])
    {
        $question->setTitle($questionData[QuestionInterface::QUESTION])
            ->setAnswer($questionData[QuestionInterface::ANSWER])
            ->setUrlKey(strtolower($questionData[QuestionInterface::URL_KEY]));

        if (!empty($questionData[QuestionInterface::SHORT_ANSWER])) {
            $question->setShortAnswer($questionData[QuestionInterface::SHORT_ANSWER]);
        }

        if (!empty($questionData[QuestionInterface::NAME])) {
            $question->setName($questionData[QuestionInterface::NAME]);
        }

        if (!empty($questionData[QuestionInterface::EMAIL])) {
            $question->setEmail($questionData[QuestionInterface::EMAIL]);
        }

        $stores = [];
        if (!empty($questionData[QuestionInterface::STORE_CODES])) {
            $storeCodes = explode(
                self::MULTI_VALUE_SEPARATOR,
                $questionData[QuestionInterface::STORE_CODES]
            );
            foreach ($storeCodes as $code) {
                $stores[] = $this->stores[trim($code)];
            }
        }
        if (empty($stores)) {
            $stores[] = $this->storeManager->getDefaultStoreView()->getId();
        }
        $question->setData(QuestionInterface::STORES, $stores);

        if (!empty($questionData[QuestionInterface::STATUS])
            && $questionData[QuestionInterface::STATUS] == Status::STATUS_ANSWERED
        ) {
            $question->setStatus(Status::STATUS_ANSWERED);
        } else {
            $question->setStatus(Status::STATUS_PENDING);
        }

        if (empty($questionData[QuestionInterface::VISIBILITY])) {
            $questionData[QuestionInterface::VISIBILITY] = Visibility::VISIBILITY_NONE;
        }
        switch ((int)$questionData[QuestionInterface::VISIBILITY]) {
            case Visibility::VISIBILITY_PUBLIC:
                $question->setVisibility(Visibility::VISIBILITY_PUBLIC);
                break;
            case Visibility::VISIBILITY_FOR_LOGGED:
                $question->setVisibility(Visibility::VISIBILITY_FOR_LOGGED);
                break;
            default:
                $question->setVisibility(Visibility::VISIBILITY_NONE);
                break;
        }

        if (!empty($questionData[QuestionInterface::META_TITLE])) {
            $question->setMetaTitle($questionData[QuestionInterface::META_TITLE]);
        }

        if (!empty($questionData[QuestionInterface::META_DESCRIPTION])) {
            $question->setMetaDescription($questionData[QuestionInterface::META_DESCRIPTION]);
        }

        if (!empty($questionData[QuestionInterface::POSITION])) {
            $question->setPosition((int)$questionData[QuestionInterface::POSITION]);
        } else {
            $question->setPosition(0);
        }

        $categories = [];
        if (!empty($questionData[QuestionInterface::CATEGORY_IDS])) {
            $categoryIds = explode(self::MULTI_VALUE_SEPARATOR, $questionData[QuestionInterface::CATEGORY_IDS]);
            foreach ($categoryIds as &$categoryId) {
                $categoryId = (int)trim($categoryId);
            }
            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection->addFieldToFilter(CategoryInterface::CATEGORY_ID, ['in' => $categoryIds]);
            $categoryCollection->addFieldToSelect([CategoryInterface::CATEGORY_ID]);
            foreach ($categoryCollection->getData() as $category) {
                $categories[] = $category[CategoryInterface::CATEGORY_ID];
            }
        }
        $question->setData(QuestionInterface::CATEGORIES, $categories);

        $products = [];
        if (!empty($questionData[QuestionInterface::PRODUCT_SKUS])) {
            $productSkus = explode(self::MULTI_VALUE_SEPARATOR, $questionData[QuestionInterface::PRODUCT_SKUS]);
            foreach ($productSkus as &$productSku) {
                $productSku = trim($productSku);
            }
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addFieldToFilter(ProductInterface::SKU, ['in' => $productSkus]);
            $productCollection->addFieldToSelect(['entity_id']);
            foreach ($productCollection->getData() as $product) {
                $products[] = $product['entity_id'];
            }
        }
        $question->setData('product_ids', $products);

        $question->setData('tag_ids', []);
    }

    private function setStores()
    {
        if (empty($this->stores)) {
            $stores = $this->storeManager->getStores(true);
            foreach ($stores as $store) {
                $this->stores[$store->getCode()] = $store->getId();
            }
        }
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }
}
