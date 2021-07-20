<?php


namespace Mageplaza\Webhook\Observer;


trait OrderAwareTrait
{
    /**
     * @param $item
     * @return
     */
    protected function getOrderFromItem($item)
    {
        return $item->getOrder();
    }
}