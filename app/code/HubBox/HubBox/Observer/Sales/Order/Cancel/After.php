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

namespace HubBox\HubBox\Observer\Sales\Order\Cancel;

use HubBox\HubBox\Api\Request;
use HubBox\HubBox\Model\OrdersFactory;
use \HubBox\HubBox\Logger\Logger as Logger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class After implements ObserverInterface
{
    protected $_request;
    protected $_hubBoxOrder;
    protected $_logger;

    public function __construct(
        OrdersFactory $hubBoxOrder,
        Request $request,
        Logger $logger
    )
    {
        $this->_hubBoxOrder = $hubBoxOrder;
        $this->_request = $request;
        $this->_logger = $logger;
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
        $order = $observer->getEvent()->getOrder();
        $hubBoxOrder = $this->_hubBoxOrder->create()->load($order->getIncrementId(), 'order_id');
        $this->_logger->info('cancelAfter: hubbox order Id: ' . $hubBoxOrder->getId());
        if ($hubBoxOrder->getId()) {
            try {
                $hubBoxParcelId = $hubBoxOrder->getHubboxParcelId();
                $this->_request->cancelParcel($hubBoxParcelId);
            } catch (\Exception $e) {
                $e->getMessage();
            }
        }
    }
}

