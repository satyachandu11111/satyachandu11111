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

namespace HubBox\HubBox\Observer\Sales\Order;

use HubBox\HubBox\Api\Request;
use HubBox\HubBox\Model\OrdersFactory;
use HubBox\HubBox\Model\QuoteFactory;
use HubBox\HubBox\Helper\Checkout;
use \HubBox\HubBox\Logger\Logger as Logger;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;

class After implements ObserverInterface
{
    /** @var Session $customer */
    protected $customer;

    /** @var CheckoutSession */
    protected $checkoutSession;
    protected $_hubBoxOrder;
    protected $_hubBoxQuote;
    protected $_helper;

    /** @var  Logger $logger */
    protected $_logger;
    protected $_request;

    public function __construct(
        Session $customer,
        CheckoutSession $checkoutSession,
        OrdersFactory $hubBoxOrder,
        QuoteFactory $hubBoxQuote,
        Request $request,
        Logger $logger,
        Checkout $helper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->customer = $customer;
        $this->_hubBoxOrder = $hubBoxOrder;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_request = $request;
        $this->_logger = $logger;
        $this->_helper = $helper;
    }
    /** @noinspection ReturnTypeCanBeDeclaredInspection */

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getData('quote_id');
        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quoteId, 'quote_id');

        if ($hubBoxQuote->getId()) {
            $this->_logger->info(
                'OrderAfter: hubbox quote found, type: '
                . $hubBoxQuote->getCollectPointType()
            );
        }

        if ($hubBoxQuote->getId()) {
            if ($hubBoxQuote->getCollectPointType() == 'hubbox') {
                $collectPointId = $hubBoxQuote->getData('hubbox_collect_point_id');

                // on last check to ensure the shipping address on the order is in fact
                // a hubbox order before we do any processing
                if ($this->isHubBoxShippingAddress($order)) {
                    // save everything in the hubbox order table in case something goes wrong with the api call
                    $hubBoxOrder = $this->_hubBoxOrder->create();
                    $orderId = $order->getIncrementId();
                    $data = [
                        'order_id' => $orderId,
                        'hubbox_collect_point_id' => $collectPointId,
                    ];
                    $hubBoxOrder->addData($data)->save();

                    if ($response = $this->_request->addParcelRequest($order, $collectPointId)) {
                        // update shipping address with hubbox owner id
                        $this->_helper->updateOrderForHubBox(
                            $order,
                            $collectPointId,
                            $response->owner->hubBoxId
                        );
                    } else {
                        $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': problem with processing parcel, address not updated.', $hubBoxOrder->toArray());
                    }
                } else {
                    $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': not a valid hubbox address, no going to process. CollectPoint: ' . $collectPointId);
                }
            } elseif ($hubBoxQuote->getCollectPointType() == 'connect') {
                $collectPointId = $hubBoxQuote->getData('hubbox_collect_point_id');

                // on last check to ensure the shipping address on the order is in fact
                // a hubbox order before we do any processing
                if ($this->isInstoreShippingAddress($order)) {
                    // save everything in the hubbox order table in case something goes wrong with the api call
                    $hubBoxOrder = $this->_hubBoxOrder->create();
                    $orderId = $order->getIncrementId();
                    $data = [
                        'order_id' => $orderId,
                        'hubbox_collect_point_id' => $collectPointId,
                    ];
                    $hubBoxOrder->addData($data)->save();

                    if ($response = $this->_request->addParcelRequest($order, $collectPointId)) {
                        // update shipping address with hubbox owner id
                        $this->_helper->updateOrderForInstore(
                            $order,
                            $collectPointId,
                            $response->owner->hubBoxId
                        );
                    } else {
                        $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': problem with processing parcel, address not updated.', $hubBoxOrder->toArray());
                    }
                } else {
                    $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': not a valid instore address, no going to process. CollectPoint: ' . $collectPointId);
                }
            } elseif ($hubBoxQuote->getCollectPointType() == 'ups') {
                $collectPointId = $hubBoxQuote->getData('hubbox_collect_point_id');

                // on last check to ensure the shipping address on the order is in fact
                // a hubbox order before we do any processing
                if ($this->isUpsShippingAddress($order)) {
                    // save everything in the hubbox order table in case something goes wrong with the api call
                    $hubBoxOrder = $this->_hubBoxOrder->create();
                    $orderId = $order->getIncrementId();
                    $data = [
                        'order_id' => $orderId,
                        'hubbox_collect_point_id' => $collectPointId,
                    ];
                    $hubBoxOrder->addData($data)->save();

                    if ($response = $this->_request->addParcelRequest($order, $collectPointId)) {
                        // update shipping address with hubbox owner id
                        $this->_helper->updateOrderForUps(
                            $order,
                            $collectPointId
                        );
                    } else {
                        $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': problem with processing parcel, address not updated.', $hubBoxOrder->toArray());
                    }
                } else {
                    $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': not a valid ups address, no going to process. CollectPoint: ' . $collectPointId);
                }
            } else {
                // this is a private cp, lets just update the name on the shipping address to the billing name

                $shippingAddress    = $order->getShippingAddress();
                $billingAddress     = $order->getBillingAddress();

                $data = [
                    "firstname"     => $billingAddress->getFirstname(),
                    "lastname"      => $billingAddress->getlastname(),
                ];

                $shippingAddress
                    ->addData($data)
                    ->save();

                $this->_logger->info('OrderAfter - ' . $hubBoxQuote->getCollectPointType() . ': ', $shippingAddress);
            }
        }
    }

    /**
     * @param $order
     * @return bool
     */
    protected function isHubBoxShippingAddress($order)
    {
        $shipping = $order->getShippingAddress();
        $companyLine = strtolower($shipping->getCompany());
        return (strpos($companyLine, 'hubbox') !== false);
    }

    /**
     * @param $order
     * @return bool
     */
    protected function isUpsShippingAddress($order)
    {
        $shipping = $order->getShippingAddress();
        $companyLine = strtolower($shipping->getCompany());
        return (strpos($companyLine, 'ups') !== false);
    }

    /**
     * @param $order
     * @return bool
     */
    protected function isInstoreShippingAddress($order)
    {
        $shipping = $order->getShippingAddress();
        $companyLine = strtolower($shipping->getCompany());
        return (strpos($companyLine, 'connect') !== false);
    }
}
