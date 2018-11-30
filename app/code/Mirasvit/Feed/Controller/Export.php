<?php
namespace Mirasvit\Feed\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Exporter;

abstract class Export extends Action
{
    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var Exporter
     */
    protected $exporter;

    public function __construct(
        FeedFactory $feedFactory,
        Exporter $exporter,
        Context $context
    ) {
        $this->feedFactory = $feedFactory;
        $this->exporter = $exporter;

        parent::__construct($context);
    }

    /**
     * Current feed model
     *
     * @return \Mirasvit\Feed\Model\Feed
     */
    protected function getFeed()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $feed = $this->feedFactory->create()->load($id);

            return $feed;
        }

        return false;
    }
}
