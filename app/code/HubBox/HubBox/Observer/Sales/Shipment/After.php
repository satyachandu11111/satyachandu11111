<?php
/**
 * HubBox Click and Collect
 * Copyright (C) 2017  2017
 *
 * This file is part of HubBox/HubBox.
 *
 * HubBox/HubBox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace HubBox\HubBox\Observer\Sales\Shipment;

use HubBox\HubBox\Api\Request;
use HubBox\HubBox\Model\OrdersFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class After implements ObserverInterface
{

    protected $_request;
    protected $_hubBoxOrder;

    public function __construct(
        OrdersFactory $hubBoxOrder,
        Request $request
    )
    {
        $this->_request = $request;
        $this->_hubBoxOrder = $hubBoxOrder;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    )
    {
        // @var $shipment \Magento\Sales\Model\Order\Shipment
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $hubBoxOrder = $this->_hubBoxOrder->create()->load($order->getIncrementId(), 'order_id');

        if ($hubBoxOrder->getId()) {
            $items = $shipment->getTracksCollection()->getItems();
            if ($items) {
                try {
                    $hubBoxParcelId = $hubBoxOrder->getHubboxParcelId();
                    $tracking = [];
                    foreach ($items as $item) {
                        $tracking['courier'] = $item->getCarrierCode();
                        $tracking['code'] = $item->getTrackNumber();
                    }
                    $this->_request->addTrackingRequest($tracking, $hubBoxParcelId);
                } catch (\Exception $e) {
                    $e->getMessage();
                }
            }
        }
    }
}
