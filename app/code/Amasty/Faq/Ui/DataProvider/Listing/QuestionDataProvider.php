<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\DataProvider\Listing;

use Amasty\Faq\Model\ResourceModel\Question\Collection;
use Amasty\Faq\Api\QuestionRepositoryInterface;

class QuestionDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * QuestionDataProvider constructor.
     *
     * @param string                      $name
     * @param string                      $primaryFieldName
     * @param string                      $requestFieldName
     * @param Collection                  $collection
     * @param QuestionRepositoryInterface $repository
     * @param array                       $meta
     * @param array                       $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        QuestionRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as $key => $question) {
            $questionData = $this->repository->getById($question['question_id'])->getData();
            $data['items'][$key] = $questionData;
        }

        return $data;
    }
}
