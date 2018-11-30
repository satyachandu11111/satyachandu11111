<?php

namespace Mirasvit\Feed\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;
use Mirasvit\Feed\Model\ResourceModel\Feed\CollectionFactory as FeedCollectionFactory;

class Menu extends AbstractMenu
{
    /**
     * @var FeedCollectionFactory
     */
    protected $feedCollectionFactory;

    /**
     * @param FeedCollectionFactory $feedCollectionFactory
     * @param Context               $context
     */
    public function __construct(
        FeedCollectionFactory $feedCollectionFactory,
        Context $context
    ) {
        $this->visibleAt(['feed']);

        $this->feedCollectionFactory = $feedCollectionFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'id'       => 'feed',
            'resource' => 'Mirasvit_Feed::feed_feed',
            'title'    => __('Feeds'),
            'url'      => $this->urlBuilder->getUrl('feed/feed'),
        ])->addItem([
            'resource' => 'Mirasvit_Feed::feed_template',
            'title'    => __('Templates'),
            'url'      => $this->urlBuilder->getUrl('feed/template'),
        ])->addItem([
            'resource' => 'Mirasvit_Feed::feed_rule',
            'title'    => __('Filters'),
            'url'      => $this->urlBuilder->getUrl('feed/rule'),
        ])->addItem([
            'resource' => 'Mirasvit_Feed::feed_report',
            'title'    => __('Reports'),
            'url'      => $this->urlBuilder->getUrl('feed/report/view'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Feed::feed_dynamic_category',
            'title'    => __('Category Mapping'),
            'url'      => $this->urlBuilder->getUrl('feed/dynamic_category'),
        ])->addItem([
            'resource' => 'Mirasvit_Feed::feed_dynamic_attribute',
            'title'    => __('Dynamic Attributes'),
            'url'      => $this->urlBuilder->getUrl('feed/dynamic_attribute'),
        ])->addItem([
            'resource' => 'Mirasvit_Feed::feed_dynamic_variable',
            'title'    => __('Dynamic Variables'),
            'url'      => $this->urlBuilder->getUrl('feed/dynamic_variable'),
        ])->addItem([
            'resource' => 'Mirasvit_Feed::feed_import',
            'title'    => __('Import/Export Data'),
            'url'      => $this->urlBuilder->getUrl('feed/import'),
        ]);

        /** @var \Mirasvit\Feed\Model\Feed $feed */
        foreach ($this->feedCollectionFactory->create() as $feed) {
            if ($feed->getName()) {
                $this->addItem([
                    'resource' => 'Mirasvit_Feed::feed_feed',
                    'title'    => $feed->getName(),
                    'url'      => $this->urlBuilder->getUrl('feed/feed/edit', ['id' => $feed->getId()]),
                ], 'feed');
            }
        }

        return $this;
    }
}