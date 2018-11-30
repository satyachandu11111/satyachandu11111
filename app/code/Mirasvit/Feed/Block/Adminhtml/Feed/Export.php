<?php

namespace Mirasvit\Feed\Block\Adminhtml\Feed;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Helper\Data as FeedHelper;

class Export extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FeedHelper
     */
    protected $dataHelper;

    /**
     * {@inheritdoc}
     * @param Registry   $registry
     * @param Context    $context
     * @param FeedHelper $dataHelper
     */
    public function __construct(
        Registry $registry,
        Context $context,
        FeedHelper $dataHelper
    ) {
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        $exportUrl = $this->dataHelper->getFeedExportUrl($this->getFeed());
        $progressUrl = $this->dataHelper->getFeedProgressUrl($this->getFeed());

        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'feed_export' => [
                            'component' => 'Mirasvit_Feed/js/feed/export',
                            'config'    => [
                                'exportUrl' => $exportUrl,
                                'id'        => $this->getFeed()->getId(),
                            ],

                            'children' => [
                                'progress' => [
                                    'component' => 'Mirasvit_Feed/js/feed/progress',
                                    'config'    => [
                                        'url' => $progressUrl,
                                        'id'  => $this->getFeed()->getId(),
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return \Mirasvit\Feed\Model\Feed
     */
    public function getFeed()
    {
        return $this->registry->registry('current_model');
    }
}
