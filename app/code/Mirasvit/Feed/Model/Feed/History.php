<?php
namespace Mirasvit\Feed\Model\Feed;

use Magento\Framework\Model\AbstractModel;

/**
 * @method int getFeedId()
 * @method $this setFeedId($id)
 * @method string getTitle()
 * @method $this setTitle($title)
 * @method string getMessage()
 * @method $this setMessage($message)
 * @method string getType()
 * @method $this setType($type)
 */
class History extends AbstractModel
{
    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @param HistoryFactory                   $historyFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry      $registry
     */
    public function __construct(
        HistoryFactory $historyFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->historyFactory = $historyFactory;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\ResourceModel\Feed\History');
    }

    /**
     * @param \Mirasvit\Feed\Model\Feed $feed
     * @param string                    $title
     * @param string                    $message
     *
     * @return $this
     */
    public function add($feed, $title, $message)
    {
        /** @var History $history */
        $history = $this->historyFactory->create();
        $history->setFeedId($feed->getId())
            ->setTitle($title)
            ->setMessage($message)
            ->setType(php_sapi_name() == 'cli' ? 'CLI Mode' : 'Manual')
            ->save();

        return $this;
    }
}
