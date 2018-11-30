<?php

namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;

class Library extends Feed
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * {@inheritdoc}
     * @param FeedFactory $feedFactory
     * @param Registry    $registry
     * @param Context     $context
     */
    public function __construct(
        FeedFactory $feedFactory,
        Registry $registry,
        Context $context
    ) {
        $this->layout = $context->getView()->getLayout();

        parent::__construct($feedFactory, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('pattern')) {
            $content = $this->layout->createBlock('Mirasvit\Feed\Block\Adminhtml\Feed\Library')
                ->setPattern($this->getRequest()->getParam('pattern'))
                ->setTemplate('Mirasvit_Feed::feed/library/preview.phtml')
                ->toHtml();
        } else {
            $content = $this->layout->createBlock('Mirasvit\Feed\Block\Adminhtml\Feed\Library')
                ->setTemplate('Mirasvit_Feed::feed/library.phtml')
                ->toHtml();
        }

        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();

        return $response
            ->setBody($content);
    }

    public function _processUrlKeys()
    {
        return true;
    }
}
