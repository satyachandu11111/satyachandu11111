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
use \HubBox\HubBox\Logger\Logger;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Request\Http;
use Zend\Http\Client;

class CollectPoints extends AbstractHelper
{
    protected $request;
    protected $_helper;
    protected $_auth;
    protected $_logger;

    public function __construct(
        Context $context,
        Data $helper,
        Http $request,
        Auth $auth,
        Logger $logger
    )
    {
        $this->_helper = $helper;
        $this->request = $request;
        $this->_auth = $auth;
        $this->_logger = $logger;
    }

    protected function getCollectPointUrl()
    {
        if ($this->_helper->hasPrivateCollectPoints()) {
            $path = $this->_helper->getHubBoxApiUrl() . "retailer/collectpoints";
        } else {
            $path = $this->_helper->getHubBoxApiUrl() . "public/collectpoints";
        }
        return $path;
    }

    /**
     *
     * Find the collect points nearest to a location
     *
     * @param $q
     * @return mixed
     * @throws \Exception
     */
    public function nearest($q)
    {
        $client = new Client();
        $queryObject = array(
            "query" => $q,
            "size" => $this->_helper->getNumberOfCollectPoints(),
            "dist" => $this->_helper->getDistanceLimit()
        );

        $query = http_build_query($queryObject);
        $client->setUri($this->getCollectPointUrl() . "/nearest?" .  $query);
        $client->setMethod('GET');

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        // if private collect points enabled, add auth
        if ($this->_helper->hasPrivateCollectPoints()) {
            $token = $this->_auth->getAccessToken();
            $headers['Authorization'] = "Bearer $token";
        }

        $client->setHeaders($headers);
        $response = $client->send();
        $request = json_decode($response->getBody());

        if ($response->getStatusCode() === 200) {
            return $request;
        } else {
            throw new \Exception("No locations found");
        }
    }


    /**
     * @param $n float
     * @param $s float
     * @param $e float
     * @param $w float
     * @return mixed
     * @throws \Exception
     */
    public function within($n, $s, $e, $w)
    {
        $client = new Client();
        $queryObject = array(
            "n" => $n,
            "s" => $s,
            "e" => $e,
            "w" => $w,
            "size" => $this->_helper->getNumberOfCollectPoints()
        );

        $query = http_build_query($queryObject);
        $client->setUri($this->getCollectPointUrl() . "/within?" .  $query);
        $client->setMethod('GET');

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        // if private collect points enabled, add auth
        if ($this->_helper->hasPrivateCollectPoints()) {
            $this->_logger->info('has private cps');
            $token = $this->_auth->getAccessToken();
            $headers['Authorization'] = "Bearer $token";
        }

        $client->setHeaders($headers);
        $response = $client->send();
        $request = json_decode($response->getBody());

        if ($response->getStatusCode() === 200) {
            return $request;
        } else {
            throw new \Exception("No locations found" . $response->getBody());
        }
    }

    /**
     * Get collect point data
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getCollectPoint($id)
    {
        $client = new Client();
        $url = $this->_helper->getHubBoxApiUrl() . "public/collectpoint/";

        //$this->_logger->info($url . $id);

        $client->setUri($url . $id);
        $client->setMethod('GET');
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        // if private collect points enabled, add auth
        if ($this->_helper->hasPrivateCollectPoints()) {
            $token = $this->_auth->getAccessToken();
            $headers['Authorization'] = "Bearer $token";
        }

        $client->setHeaders($headers);
        $response = $client->send();
        $request = json_decode($response->getBody());

        if ($response->getStatusCode() === 200) {
            return $request;
        } else {
            throw new \Exception("No location found");
        }
    }
}
