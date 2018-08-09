<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Import\Category;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Api\ImportExport\CategoryInterface;
use Amasty\Faq\Model\Import\AbstractImport;
use Amasty\Faq\Model\Import\ImportValidatorInterface;
use Amasty\Faq\Model\OptionSource\Category\Status;
use Amasty\Faq\Model\ResourceModel\Category\InsertDummyCategory;
use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Amasty\Faq\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;

class Import extends AbstractImport
{
    const ENTITY_TYPE_CODE = 'faq_category_import';

    /**
     * @var array
     */
    protected $validColumnNames = [
        CategoryInterface::CATEGORY_ID,
        CategoryInterface::TITLE,
        CategoryInterface::URL_KEY,
        CategoryInterface::STORE_CODES,
        CategoryInterface::STATUS,
        CategoryInterface::META_TITLE,
        CategoryInterface::META_DESCRIPTION,
        CategoryInterface::POSITION,
        CategoryInterface::QUESTION_IDS
    ];

    protected $masterAttributeCode = CategoryInterface::TITLE;

    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var array
     */
    private $stores = [];

    /**
     * @var CollectionFactory
     */
    private $questionCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var InsertDummyCategory
     */
    private $dummyCategory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        CategoryRepositoryInterface $repository,
        CategoryFactory $categoryFactory,
        CollectionFactory $questionCollectionFactory,
        InsertDummyCategory $dummyCategory,
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
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->resource = $resource;
        $this->dummyCategory = $dummyCategory;
    }

    /**
     * @param array $data
     */
    protected function customBehaviorData(array $data)
    {
        $this->setStores();
        foreach ($data as $categoryData) {
            $category = $this->categoryFactory->create();
            $this->setCategoryData($category, $categoryData);
            try {
                $this->repository->save($category);
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
        $categoriesToCreate = [];
        foreach ($data as $categoryData) {
            $category = null;
            $categoryData[CategoryInterface::CATEGORY_ID] = (int)$categoryData[CategoryInterface::CATEGORY_ID];
            if (!empty($categoryData[CategoryInterface::CATEGORY_ID])) {
                try {
                    $category = $this->repository->getById($categoryData[CategoryInterface::CATEGORY_ID]);
                } catch (NoSuchEntityException $e) {
                    $dummyCategory = $this->categoryFactory->create();
                    $dummyCategory->setCategoryId($categoryData[CategoryInterface::CATEGORY_ID]);
                    $this->dummyCategory->save($dummyCategory);
                    try {
                        $category = $this->repository->getById($categoryData[CategoryInterface::CATEGORY_ID]);
                    } catch (NoSuchEntityException $e) {

                    }
                }

                if ($category) {
                    $this->setCategoryData($category, $categoryData);
                    try {
                        $this->repository->save($category);
                    } catch (CouldNotSaveException $e) {

                    }
                }
            } else {
                $categoriesToCreate[] = $categoryData;
            }
        }

        if (!empty($categoriesToCreate)) {
            $this->customBehaviorData($categoriesToCreate);
        }
    }

    /**
     * @param array $data
     */
    protected function deleteBehaviorData(array $data)
    {
        foreach ($data as $category) {
            if (!empty($category[CategoryInterface::CATEGORY_ID])) {
                try {
                    $this->repository->deleteById((int)$category[CategoryInterface::CATEGORY_ID]);
                } catch (CouldNotDeleteException $e) {

                }
            }
        }
    }

    /**
     * @param \Amasty\Faq\Model\Category $category
     * @param array                      $categoryData
     */
    private function setCategoryData(\Amasty\Faq\Model\Category $category, $categoryData = [])
    {
        $category->setTitle($categoryData[CategoryInterface::TITLE])
            ->setUrlKey(strtolower($categoryData[CategoryInterface::URL_KEY]));

        $stores = [];
        if (!empty($categoryData[CategoryInterface::STORE_CODES])) {
            $storeCodes = explode(
                self::MULTI_VALUE_SEPARATOR,
                $categoryData[CategoryInterface::STORE_CODES]
            );
            foreach ($storeCodes as $code) {
                $stores[] = $this->stores[trim($code)];
            }
        }
        if (empty($stores)) {
            $stores[] = $this->storeManager->getDefaultStoreView()->getId();
        }
        $category->setData('store_ids', $stores);

        if (!empty($categoryData[CategoryInterface::STATUS])
            && $categoryData[CategoryInterface::STATUS] == Status::STATUS_ENABLED
        ) {
            $category->setStatus(Status::STATUS_ENABLED);
        } else {
            $category->setStatus(Status::STATUS_DISABLED);
        }

        if (!empty($categoryData[CategoryInterface::META_TITLE])) {
            $category->setMetaTitle($categoryData[CategoryInterface::META_TITLE]);
        }

        if (!empty($categoryData[CategoryInterface::META_DESCRIPTION])) {
            $category->setMetaDescription($categoryData[CategoryInterface::META_DESCRIPTION]);
        }

        if (!empty($categoryData[CategoryInterface::POSITION])) {
            $category->setPosition((int)$categoryData[CategoryInterface::POSITION]);
        } else {
            $category->setPosition(0);
        }

        $questions = [];
        if (!empty($categoryData[CategoryInterface::QUESTION_IDS])) {
            $questionIds = explode(
                self::MULTI_VALUE_SEPARATOR,
                $categoryData[CategoryInterface::QUESTION_IDS]
            );
            foreach ($questionIds as &$questionId) {
                $questionId = (int)trim($questionId);
            }
            $questionCollection = $this->questionCollectionFactory->create();
            $questionCollection->addFieldToFilter(QuestionInterface::QUESTION_ID, ['in' => $questionIds]);
            $questionCollection->addFieldToSelect([QuestionInterface::QUESTION_ID]);
            foreach ($questionCollection->getData() as $question) {
                $questions[] = $question[QuestionInterface::QUESTION_ID];
            }
        }
        $category->setData('questions', $questions);
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
