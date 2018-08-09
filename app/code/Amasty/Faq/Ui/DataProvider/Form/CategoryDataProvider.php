<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\DataProvider\Form;

use Amasty\Faq\Model\ResourceModel\Category\Collection;
use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory as QuestionCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var QuestionCollectionFactory
     */
    private $questionCollectionFactory;

    /**
     * @var \Amasty\Faq\Model\ImageProcessor
     */
    private $imageProcessor;

    /**
     * CategoryDataProvider constructor.
     *
     * @param string                      $name
     * @param string                      $primaryFieldName
     * @param string                      $requestFieldName
     * @param Collection                  $collection
     * @param CategoryRepositoryInterface $repository
     * @param DataPersistorInterface      $dataPersistor
     * @param QuestionCollectionFactory   $questionCollectionFactory
     * @param array                       $meta
     * @param array                       $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        CategoryRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor,
        QuestionCollectionFactory $questionCollectionFactory,
        \Amasty\Faq\Model\ImageProcessor $imageProcessor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();

        /**
         * It is need for support of several fieldsets.
         * For details @see \Magento\Ui\Component\Form::getDataSourceData
         */
        if ($data['totalRecords'] > 0) {
            $categoryId = (int)$data['items'][0]['category_id'];
            $model = $this->repository->getById($categoryId);
            $categoryData = $model->getData();
            if ($model->getIcon()) {
                $categoryData['icon_file'] = [
                    [
                        'name' => $model->getIcon(),
                        'url' => $this->imageProcessor->getCategoryIconUrl($model->getIcon())
                    ]
                ];
            }
            $data[$categoryId] = $categoryData;
            $data[$categoryId]['links']['questions'] = $this->getCategoryQuestions($categoryId);
        }

        if ($savedData = $this->dataPersistor->get('categoryData')) {
            $savedCategoryId = isset($savedData['category_id']) ? $savedData['category_id'] : null;
            if (isset($data[$savedCategoryId])) {
                $data[$savedCategoryId] = array_merge($data[$savedCategoryId], $savedData);
            } else {
                $data[$savedCategoryId] = $savedData;
            }
            $this->dataPersistor->clear('categoryData');
        }

        return $data;
    }

    /**
     * @param int $categoryId
     *
     * @return array
     */
    private function getCategoryQuestions($categoryId = 0)
    {
        $questionCollection = $this->questionCollectionFactory->create()
            ->addCategoryFilter($categoryId)
            ->setOrder('position', 'ASC')
            ->setOrder('question_id', 'ASC');

        return $questionCollection->getData();
    }
}
