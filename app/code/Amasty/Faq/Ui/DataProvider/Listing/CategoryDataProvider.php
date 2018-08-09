<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\DataProvider\Listing;

use Amasty\Faq\Model\ResourceModel\Category\Collection;
use Amasty\Faq\Api\CategoryRepositoryInterface;

class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * CategoryDataProvider constructor.
     *
     * @param string                      $name
     * @param string                      $primaryFieldName
     * @param string                      $requestFieldName
     * @param Collection                  $collection
     * @param CategoryRepositoryInterface $repository
     * @param array                       $meta
     * @param array                       $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        CategoryRepositoryInterface $repository,
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
        foreach ($data['items'] as $key => $category) {
            $categoryData = $this->repository->getById($category['category_id'])->getData();
            $data['items'][$key] = $categoryData;
        }

        return $data;
    }
}
