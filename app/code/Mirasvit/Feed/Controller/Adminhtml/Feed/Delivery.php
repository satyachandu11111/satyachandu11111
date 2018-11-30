<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Deliverer;

class Delivery extends Feed
{
    /**
     * @var Deliverer
     */
    protected $deliverer;

    /**
     * {@inheritdoc}
     * @param Deliverer   $deliverer
     * @param FeedFactory $feedFactory
     * @param Registry    $registry
     * @param Context     $context
     */
    public function __construct(
        Deliverer $deliverer,
        FeedFactory $feedFactory,
        Registry $registry,
        Context $context
    ) {
        $this->deliverer = $deliverer;

        parent::__construct($feedFactory, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $feed = $this->initModel();

        try {
            $this->deliverer->delivery($feed);
            $this->messageManager->addSuccess(__('Feed was successfully delivered to "%1"', $feed->getFtpHost()));
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Unable to delivery feed. %1', $e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $feed->getId()]);
    }
}
