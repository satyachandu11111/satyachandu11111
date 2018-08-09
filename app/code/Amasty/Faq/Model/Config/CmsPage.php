<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Config;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Option\ArrayInterface;

class CmsPage implements ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    private $collection;

    /**
     * @var array
     */
    private $pages;

    /**
     * CmsPage constructor.
     *
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
    ) {
        $this->collection = $collectionFactory->create();
    }


    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->pages) {
            $this->collection->addFieldToSelect([PageInterface::PAGE_ID, PageInterface::TITLE]);
            foreach ($this->collection->getData() as $page) {
                $this->pages[] = ['value' => $page[PageInterface::PAGE_ID], 'label' => $page[PageInterface::TITLE]];
            }
        }

        return $this->pages;
    }
}
