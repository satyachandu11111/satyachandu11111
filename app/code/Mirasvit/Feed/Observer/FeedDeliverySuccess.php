<?php
namespace Mirasvit\Feed\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Feed\Model\Feed\Notifier;

class FeedDeliverySuccess implements ObserverInterface
{
    /**
     * @var Notifier
     */
    protected $notifier;

    /**
     * Constructor
     *
     * @param Notifier $notifier
     */
    public function __construct(
        Notifier $notifier
    ) {
        $this->notifier = $notifier;
    }

    /**
     * {@inheritdoc}
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $feed = $observer->getData('feed');

        $this->notifier->deliverySuccess($feed);
    }
}
