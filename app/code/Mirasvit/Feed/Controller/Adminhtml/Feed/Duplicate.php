<?php

namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Copier;

class Duplicate extends Feed
{
    /**
     * @var Copier
     */
    protected $copier;

    /**
     * {@inheritdoc}
     * @param Copier      $copier
     * @param FeedFactory $feedFactory
     * @param Registry    $registry
     * @param Context     $context
     */
    public function __construct(
        Copier $copier,
        FeedFactory $feedFactory,
        Registry $registry,
        Context $context
    ) {
        $this->copier = $copier;

        parent::__construct($feedFactory, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $feed = $this->initModel();
            $this->copier->copy($feed);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }

        $this->messageManager->addSuccess(__('Feed was successfully duplicated.'));
        return $resultRedirect->setPath('*/*/');
    }
}
