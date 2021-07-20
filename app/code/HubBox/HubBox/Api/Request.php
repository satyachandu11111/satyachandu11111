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

namespace HubBox\HubBox\Api;

use HubBox\HubBox\Helper\Data;
use HubBox\HubBox\Model\OrdersFactory;
use \HubBox\HubBox\Logger\Logger;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Zend\Http\Client;

class Request extends AbstractHelper
{
    const NUMBER_OF_ATTEMPTS = 5;

    protected $_helper;
    protected $_auth;
    protected $_ordersFactory;
    protected $_logger;

    protected $_objectManagerInterface;
    protected $_dateTime;

    public function __construct(
        Context $context,
        Data $helper,
        Auth $auth,
        OrdersFactory $ordersFactory,
        Logger $logger
    )
    {
        $this->_helper = $helper;
        $this->_auth = $auth;
        $this->_ordersFactory = $ordersFactory;
        $this->_logger = $logger;
    }

    /**
     * @param $order
     * @param $collectPointId
     * @return string
     */
    public function addParcelRequest($order, $collectPointId)
    {
        $client = new Client();
        $products = $this->_getProducts($order);
        $street = $order->getBillingAddress()->getStreet();

        $body = [
            'collectPointId' => $collectPointId,
            'customer' => [
                'firstName' => $order->getBillingAddress()->getFirstname(),
                'lastName' => $order->getBillingAddress()->getLastname(),
                'email' => $order->getBillingAddress()->getEmail(),
                'telephone' => $order->getBillingAddress()->getTelephone(),
                'billingAddress' => [
                    'street1' => $street[0],
                    'street2' => (count($order->getBillingAddress()->getStreet()) > 1) ? $street[1] : '',
                    'street3' => '',
                    'street4' => '',
                    'postcode' => $order->getBillingAddress()->getPostcode(),
                    'country' => 'United Kingdom',
                    'county' => $order->getBillingAddress()->getRegion()
                ],
            ],
            'order' => [
                'id' => $order->getIncrementId()
            ],
            'products' => $products,
            'shippingMethod' => $order->getShippingDescription() . " : £" . $order->getShippingAmount()
        ];

        $client->setUri($this->_helper->getHubBoxApiUrl() . 'retailer/parcels');
        $client->setMethod('POST');
        $token = $this->_auth->getAccessToken();

        /**
         * For requests to /oauth/token the basic auth header needs to be set to the "platform key"
         * these are the details for magento.
         *
         * For other api requests the auth header should be Bearer $accessToken
         *
         */
        $client->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token"
        ]);

        $client->setRawBody(json_encode($body));

        try {
            $response = $client->send();
            $request = json_decode($response->getBody());

            if ($response->getStatusCode() === 200) {
                $this->_processHubBoxOrder($request, $order);
                $this->_logger->info('API: add parcel request successful');
                return $request;
            }else{
                $this->_logger->info('API: add parcel request failed' . json_encode($request));
            }
        } catch (\Exception $e) {
            //$e->getMessage();
            $this->_logger->info('API: catch error: ' . print_r($e->getMessage(),true));
        }
        return false;
    }

    /**
     * @param $hubBoxParcelId
     * @return string
     */
    public function cancelParcel($hubBoxParcelId)
    {
        $client = new Client();

        $body = [
            'reason' => "n/a"
        ];

        $client->setUri($this->_helper->getHubBoxApiUrl() . 'retailer/parcel/' . $hubBoxParcelId . '/cancel');
        $client->setMethod('POST');
        $token = $this->_auth->getAccessToken();

        $client->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token"
        ]);

        $client->setRawBody(json_encode($body));

        try {
            $response = $client->send();
            $request = json_decode($response->getBody());

            if ($response->getStatusCode() === 200) {
                $this->_logger->info('API: cancel parcel successful ' . print_r($body,true));
            }else{
                $this->_logger->info('API: cancel parcel request failed ' . print_r($request,true));
            }
        } catch (\Exception $e) {
            $this->_logger->info('API: catch error: ' . print_r($e->getMessage(),true));
        }

    }

    /**
     * @param $order
     * @return array
     */
    public function _getProducts($order): array
    {
        $products = [];
        $items = $order->getItems();

        foreach ($items as $item) {
            $product = [];
            /** @var  $item */
            $product['name'] = $item->getName();
            $product['sku'] = $item->getSku();
            $product['price'] = $item->getPrice();
            $products[] = $product;
        }
        return $products;
    }

    /**
     * @param $request
     * @param $order
     * @throws \Exception
     */
    public function _processHubBoxOrder($request, $order)
    {
        $hubBoxOrder = $this->_ordersFactory->create()->load($order->getIncrementId(), 'order_id');
        if ($hubBoxOrder->getOrderId()) {
            $data = [
                'processed' => 1,
                'hubbox_parcel_id' => $request->id,
                'hubbox_collection_code' => $request->owner->hubBoxId,
            ];
            $hubBoxOrder->addData($data)->save();
        }
    }

    public function addTrackingRequest($tracking, $hubBoxParcelId)
    {
        $this->_logger->info('API: addTrackingRequest');
        $client = new Client();

        $body = [
            'trackingCodes' => $tracking
        ];

        $client->setUri($this->_helper->getHubBoxApiUrl() . 'retailer/parcel/' . $hubBoxParcelId . '/add-tracking');
        $client->setMethod('POST');
        $token = $this->_auth->getAccessToken();

        /**
         * For requests to /oauth/token the basic auth header needs to be set to the "platform key"
         * these are the details for magento.
         *
         * For other api requests the auth header should be Bearer $accessToken
         *
         */
        $client->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token"
        ]);

        $client->setRawBody(json_encode($body));

        $response = $client->send();
        $request = json_decode($response->getBody());

        $this->_logger->info('API: addTrackingRequest result'.$response->getBody());
        return $response ? $request : '';
    }
}
