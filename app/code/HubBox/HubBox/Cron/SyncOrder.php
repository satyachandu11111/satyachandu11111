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

namespace HubBox\HubBox\Cron;

use HubBox\HubBox\Model\OrdersFactory;
use HubBox\HubBox\Api\Request;
use HubBox\HubBox\Logger\Logger as Logger;
use HubBox\HubBox\Helper\Checkout;

use Magento\Sales\Model\OrderFactory;

class SyncOrder
{

    protected $_logger;
    protected $_ordersFactory;
    protected $_salesOrderFactory;
    protected $_apiRequest;
    protected $_httpRequest;
    protected $_helper;

    const NUMBER_OF_ATTEMPTS = 5;

    /**
     * SyncOrder constructor.
     * @param Logger $logger
     * @param Request $apiRequest
     * @param OrdersFactory $ordersFactory
     * @param OrderFactory $salesOrderFactory
     * @param Checkout $helper
     */
    public function __construct(
        Logger $logger,
        Request $apiRequest,
        OrdersFactory $ordersFactory,
        OrderFactory $salesOrderFactory,
        Checkout $helper
    )
    {
        $this->_logger = $logger;
        $this->_apiRequest = $apiRequest;
        $this->_ordersFactory = $ordersFactory;
        $this->_salesOrderFactory = $salesOrderFactory;
        $this->_helper = $helper;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $hubBoxOrders = $this->_ordersFactory->create()->getCollection()->addFieldToFilter('processed', 0);
        $this->_logger->info('syncOrders called #'.count($hubBoxOrders));

        if ($hubBoxOrders) {
            foreach ($hubBoxOrders as $hubBoxOrder) {
                $this->_logger->info('syncOrders hubboxorder #'.$hubBoxOrder->getData('id').' orderid:'.$hubBoxOrder->getData('order_id'));

                $order = $this->_salesOrderFactory->create()->loadByIncrementId($hubBoxOrder->getData('order_id'));
                if ($hubBoxOrder->getData('attempts') < self::NUMBER_OF_ATTEMPTS) {
                    $collectPointId = $hubBoxOrder->getData('hubbox_collect_point_id');
                    $hubBoxOrder->setAttempts($hubBoxOrder->getData('attempts') + 1)->save();

                    if ($response = $this->_apiRequest->addParcelRequest($order, $collectPointId)) {
                        // update shipping address with hubbox owner id
                        $this->_helper->updateOrderShipping(
                            $order,
                            $collectPointId,
                            $response->owner->hubBoxId
                        );
                    } else {
                        $this->_logger->info('Cron: problem with processing parcel, address not updated');
                    }
                    $this->_apiRequest->addParcelRequest($order, $collectPointId);
                }
            }
        }
    }
}
