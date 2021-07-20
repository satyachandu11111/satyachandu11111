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

namespace HubBox\HubBox\Helper;

use HubBox\HubBox\Api\CollectPoints;
use HubBox\HubBox\Logger\Logger as Logger;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Checkout extends AbstractHelper
{
    protected $storeManager;
    protected $objectManager;
    protected $_helper;
    protected $_api;

    /**
     * Checkout constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param \HubBox\HubBox\Helper\Data $helper
     * @param CollectPoints $collectPoints
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Data $helper,
        CollectPoints $collectPoints,
        Logger $logger
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_api = $collectPoints;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Modifies an order for HubBox
     * @param \Magento\Sales\Model\Order $order
     * @param $collectPointId
     * @param null $hubBoxId
     * @return bool
     */
    public function updateOrderForHubBox($order, $collectPointId, $hubBoxId = null)
    {
        try {
            $shippingAddress = $order->getShippingAddress();
            $billingAddress = $order->getBillingAddress();
            $updateSlug = $this->_helper->getLabelAppend();
            $cp = $this->_api->getCollectPoint($collectPointId);

            $data = [
                "firstname" => $billingAddress->getFirstname(),
                "lastname" => $billingAddress->getlastname(),
                "street" => [
                    $cp->address->street1,
                    $cp->address->street2
                ],
                "city" => $cp->address->city,
                "region" => $cp->address->region,
                "country_id" => 'GB',
                "postcode" => $cp->address->postcode,
                "email" => $order->getCustomerEmail(),
                "company" => $cp->shortName . ' HubBox',
                "telephone" => "02078594577",
                "save_in_address_book" => 0
            ];

            // first replace, replace first name with HubBox id,
            // second name should have first initial prepended.
            if ($updateSlug == 'first_replace') {
                $firstInitial = substr($billingAddress->getFirstname(), 0, 1);
                $shippingAddress
                    ->addData($data)
                    ->setData("firstname", $hubBoxId)
                    ->setData("lastname", $firstInitial . ' ' . $billingAddress->getlastname())
                    ->save();
            } else {
                // do normal append
                $shippingAddress
                    ->addData($data)
                    ->setData(
                        $updateSlug,
                        $shippingAddress->getData($updateSlug) . ' ' . $hubBoxId
                    )
                    ->save();
            }
        } catch (\Exception $exception) {
            $this->_logger->info('helper/updateOrderShipping problem getting collect point data for HubBox');
            return false;
        }
        return true;
    }

    /**
     * Update order for UPS Access Point delivery
     * @param $order
     * @param $collectPointId
     * @return bool
     */
    public function updateOrderForUps($order, $collectPointId)
    {
        try {
            $shippingAddress = $order->getShippingAddress();
            $billingAddress = $order->getBillingAddress();
            $cp = $this->_api->getCollectPoint($collectPointId);
            $data = [
                "firstname" => $billingAddress->getFirstname(),
                "lastname" => $billingAddress->getlastname(),
                "street" => [
                    $cp->address->street1,
                    $cp->address->street2
                ],
                "city" => $cp->address->city,
                "region" => $cp->address->region,
                "country_id" => 'US',
                "postcode" => $cp->address->postcode,
                "email" => $order->getCustomerEmail(),
                "company" => $cp->shortName . ' UPS S2AP',
                "telephone" => "02078594577",
                "save_in_address_book" => 0
            ];
            $shippingAddress
                ->addData($data)
                ->setData("firstname", $billingAddress->getFirstname)
                ->setData("lastname", $billingAddress->getlastname())
                ->save();
        } catch (\Exception $exception) {
            $this->_logger->info('helper/updateOrderShipping problem getting collect point data for UPS');
            return false;
        }
        return true;
    }

    /**
     * Updates an order for using HubBox Instore system
     * @param $order
     * @param $collectPointId
     * @param null $hubBoxId
     * @return bool
     */
    public function updateOrderForInstore($order, $collectPointId, $hubBoxId = null)
    {
        try {
            $shippingAddress = $order->getShippingAddress();
            $billingAddress = $order->getBillingAddress();
            $updateSlug = $this->_helper->getLabelAppend();
            $cp = $this->_api->getCollectPoint($collectPointId);

            $data = [
                "firstname" => $billingAddress->getFirstname(),
                "lastname" => $billingAddress->getlastname(),
                "street" => [
                    $cp->address->street1,
                    $cp->address->street2
                ],
                "city" => $cp->address->city,
                "region" => $cp->address->region,
                "country_id" => 'GB', // TODO international connect
                "postcode" => $cp->address->postcode,
                "email" => $order->getCustomerEmail(),
                "company" => $cp->shortName . ' Connect',
                "telephone" => "02078594577",
                "save_in_address_book" => 0
            ];

            // first replace, replace first name with HubBox id,
            // second name should have first initial prepended.
            if ($updateSlug == 'first_replace') {
                $firstInitial = substr($billingAddress->getFirstname(), 0, 1);
                $shippingAddress
                    ->addData($data)
                    ->setData("firstname", $hubBoxId)
                    ->setData("lastname", $firstInitial . ' ' . $billingAddress->getlastname())
                    ->save();
            } else {
                // do normal append
                $shippingAddress
                    ->addData($data)
                    ->setData(
                        $updateSlug,
                        $shippingAddress->getData($updateSlug) . ' ' . $hubBoxId
                    )
                    ->save();
            }
        } catch (\Exception $exception) {
            $this->_logger->info('helper/updateOrderShipping problem getting collect point data for HubBox');
            return false;
        }
        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param $collectPointId
     * @return bool
     * @throws \Exception
     */
    public function updateQuoteShipping($quote, $collectPointId)
    {
        $shippingAddress = $quote->getShippingAddress();
        try {
            $cp = $this->_api->getCollectPoint($collectPointId);
            $data = [
                "firstname" => "-",
                "lastname" => "-",
                "street" => [
                    $cp->address->street1,
                    $cp->address->street2
                ],
                "city" => $cp->address->city,
                "region" => $cp->address->region,
                "country_id" => "GB",
                "postcode" => $cp->address->postcode,
                "email" => $quote->getCustomerEmail(),
                "company" => $cp->shortName . ' HubBox',
                "telephone" => "02078594577",
                "save_in_address_book" => 0
            ];

            $shippingAddress
                ->addData($data)
                ->save();
        } catch (\Exception $exception) {
            $this->_logger->info('helper/updateQuoteShipping problem getting collect point data');
            return false;
        }
        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function noHubBox($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $data = [
              "firstname" => "-",
              "lastname" => "-",
              "street" => [
                  "",
                  ""
              ],
              "city" => "",
              "region" => "",
              "country_id" => "",
              "postcode" => "",
              "email" => $quote->getCustomerEmail(),
              "company" => "",
              "telephone" => ""
          ];
        $shippingAddress
              ->addData($data)
              ->save();
    }
}
