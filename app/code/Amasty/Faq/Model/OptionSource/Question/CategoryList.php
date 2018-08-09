<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\OptionSource\Question;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\Faq\Model\ResourceModel\Category\CollectionFactory;

class CategoryList implements OptionSourceInterface
{
    /**
     * @var \Amasty\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $categories = [];
        $collection = $this->collectionFactory->create();
        foreach ($collection as $category) {
            $categories[] = [
                'value' => $category->getCategoryId(),
                'label' => $category->getTitle()
            ];
        }

        return $categories;
    }
}
