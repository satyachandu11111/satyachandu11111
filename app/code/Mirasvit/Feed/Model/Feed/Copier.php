<?php
namespace Mirasvit\Feed\Model\Feed;

use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\FeedFactory;

class Copier
{
    /**
     * Feed Factory
     *
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * Constructor
     *
     * @param FeedFactory $feedFactory
     */
    public function __construct(
        FeedFactory $feedFactory
    ) {
        $this->feedFactory = $feedFactory;
    }

    /**
     * Create new copy of feed
     *
     * @param Feed $feed
     * @return Feed
     */
    public function copy(Feed $feed)
    {
        $copy = $this->feedFactory->create()
            ->setData($feed->getData())
            ->setId(null)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setGeneratedAt(null)
            ->setGeneratedCnt(null)
            ->setGeneratedTime(null)
            ->setUploadedAt(null)
            ->setRuleIds(null)
            ->setName($feed->getName() . ' copy')
            ->setFilename($feed->getData('filename') . '_copy')
            ->save();

        $copy->setRuleIds($feed->getRuleIds())
            ->save();

        return $copy;
    }
}
