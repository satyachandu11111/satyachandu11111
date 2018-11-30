<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Controller\Adminhtml\Feed;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Exporter;

class Progress extends Feed
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * {@inheritdoc}
     * @param Exporter       $exporter
     * @param FeedFactory    $feedFactory
     * @param Registry       $registry
     * @param Context        $context
     */
    public function __construct(
        Exporter $exporter,
        FeedFactory $feedFactory,
        Registry $registry,
        Context $context
    ) {
        $this->exporter = $exporter;

        parent::__construct($feedFactory, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $feed = $this->initModel();

        $progress = $this->exporter->getHandler($feed)->toJson();

        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();
        $response->representJson(\Zend_Json::encode($progress));
    }
}
