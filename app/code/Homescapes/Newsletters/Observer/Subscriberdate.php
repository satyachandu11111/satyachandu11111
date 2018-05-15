<?php

namespace Homescapes\Newsletters\Observer;

use Magento\Framework\Event\ObserverInterface;

class Subscriberdate implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $event = $observer->getEvent();
        $subscriber = $event->getDataObject();
        $data = $subscriber->getData();
        $data['subscriber_date']=date("Y-m-d");
        $subscriber->setData($data);
    }

}
