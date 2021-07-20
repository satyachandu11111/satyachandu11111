<?php

namespace Mageplaza\Webhook\Observer;

use Mageplaza\Webhook\Model\Config\Source\HookType;

/**
 * Class AfterOrder
 * @package Mageplaza\Webhook\Observer
 */
class AfterCancelOrder extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::CANCEL_ORDER;

    /**
     * @param $observer
     * @return
     */
    protected function getDataObjectFromObserver($observer)
    {
        return $observer->getEvent()->getOrder();
    }
}
