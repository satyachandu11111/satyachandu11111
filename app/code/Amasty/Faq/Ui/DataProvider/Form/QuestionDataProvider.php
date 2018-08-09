<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\DataProvider\Form;

use Amasty\Faq\Model\ResourceModel\Question;
use Amasty\Faq\Model\ResourceModel\Question\Collection;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Utils\Price;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;

class QuestionDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Question
     */
    private $question;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var Price
     */
    private $priceModifier;

    /**
     * QuestionDataProvider constructor.
     *
     * @param string                      $name
     * @param string                      $primaryFieldName
     * @param string                      $requestFieldName
     * @param Collection                  $collection
     * @param QuestionRepositoryInterface $repository
     * @param DataPersistorInterface      $dataPersistor
     * @param Question                    $question
     * @param ProductCollectionFactory    $productCollectionFactory
     * @param ImageHelper                 $imageHelper
     * @param Price                       $priceModifier
     * @param array                       $meta
     * @param array                       $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        QuestionRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor,
        Question $question,
        ProductCollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        Price $priceModifier,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->question = $question;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->priceModifier = $priceModifier;
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
            $questionId = (int)$data['items'][0]['question_id'];
            $questionModel = $this->repository->getById($questionId);
            $questionModel->getTagTitles();
            $questionData = $questionModel->getData();
            $data[$questionId] = $questionData;
            $data[$questionId]['links']['products'] = $this->getQuestionProducts($questionId);
        }

        if ($savedData = $this->dataPersistor->get('questionData')) {
            $savedQuestionId = isset($savedData['question_id']) ? $savedData['question_id'] : null;
            if (isset($data[$savedQuestionId])) {
                $data[$savedQuestionId] = array_merge($data[$savedQuestionId], $savedData);
            } else {
                $data[$savedQuestionId] = $savedData;
            }
            $this->dataPersistor->clear('questionData');
        }

        return $data;
    }

    /**
     * @param int $questionId
     *
     * @return array|null
     */
    private function getQuestionProducts($questionId = 0)
    {
        if ($productIds = $this->question->getProductIds($questionId)) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addIdFilter($productIds)
                ->addAttributeToSelect(['status', 'thumbnail', 'name', 'price'], 'left');

            $result = [];
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            foreach ($productCollection->getItems() as $product) {
                $result[] = $this->fillData($product);
            }

            return $result;
        }

        return null;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     */
    private function fillData(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return [
            'entity_id' => $product->getId(),
            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            'name' => $product->getName(),
            'status' => $product->getStatus(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice() ? $this->priceModifier->toDefaultCurrency($product->getPrice()) : ''
        ];
    }
}
