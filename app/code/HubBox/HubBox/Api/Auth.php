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
use HubBox\HubBox\Model\AuthFactory;
use \HubBox\HubBox\Logger\Logger;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Helper\AbstractHelper;

use Magento\Framework\App\Request\Http;
use Zend\Http\Client;

class Auth extends AbstractHelper
{

    const USERNAME = 'magento';
    const PASSWORD = 'gh74nd0mt0vn8fn9le02kd0gwlfns';

    const NUMBER_OF_ATTEMPTS = 5;

    protected $request;
    protected $_helper;
    protected $_authFactory;
    protected $_logger;

    public function __construct(
        Context $context,
        Data $helper,
        Http $request,
        AuthFactory $authFactory,
        Logger $logger
    )
    {
        $this->_helper = $helper;
        $this->request = $request;
        $this->_authFactory = $authFactory;
        $this->_logger = $logger;
    }


    /**
     * check if token is ok, we may need a refresh
     * @param $accessToken
     * @return bool
     */
    public function isValidToken($accessToken)
    {
        if (!$accessToken) {
            return false;
        }

        $expiresIn = (int) $accessToken->getData('expires_in');
        $timestamp = strtotime($accessToken->getData('timestamp'));

        // check if token exists or is still warm
        if ($timestamp + $expiresIn < time()) {
            return false;
        }

        return true;
    }

    /**
     * Get last access token from the db,
     * do a quick check to see if its expired and try a refresh
     *
     * @return string access_token
     */
    public function getAccessToken()
    {
        $accessToken = $this->_authFactory->create()->getCollection()->getLastItem();
        if (!$this->isValidToken($accessToken)) {
            $this->refreshToken();
            $accessToken = $this->_authFactory->create()->getCollection()->getLastItem();
        }

        return $accessToken->getData('access_token');
    }


    public function refreshToken()
    {
        $client = new Client();
        $timestamp = date('Y-m-d H:i:s');

        $username = $this->_helper->getHubBoxApiUsername();
        $password = $this->_helper->getHubBoxApiKey();

        // check credentials are present before attempting to get
        if ((!strlen($username)>0) || (!strlen($password)>0)) {
            $this->_logger->info('Credentials not present');
            return false;
        }

        $params = [
            'grant_type' => 'password',
            'username' => $this->_helper->getHubBoxApiUsername(),
            'password' => $this->_helper->getHubBoxApiKey()
        ];

        $client->setUri($this->_helper->getHubBoxApiUrl(). 'oauth/token');
        $client->setMethod('POST');

        $client->setHeaders([
            'Accept' => 'application/json'
        ]);

        /**
         * For requests to /oauth/token the basic auth header needs to be set to the "platform key"
         * these are the details for magento.
         *
         * For other api requests the auth header should be Bearer $accessToken
         *
         */
        $client->setAuth(self::USERNAME, self::PASSWORD);
        $client->setParameterPost($params);

        $a = 0;
        $success = false;

        while ($a < self::NUMBER_OF_ATTEMPTS) {
            try {
                $response = $client->send();
                $request = json_decode($response->getBody());
                if ($response->getStatusCode() === 200) {
                    $data = [
                        'access_token' => $request->access_token,
                        'refresh_token' => $request->refresh_token,
                        'token_type' => $request->token_type,
                        'expires_in' => $request->expires_in,
                        'scope' => $request->scope,
                        'jti' => $request->jti,
                        'timestamp' => $timestamp
                    ];
                    $this->_authFactory->create()->setData($data)->save();
                    $success = true;
                    break;
                } else {
                    $this->_logger->error('API Auth token refresh bad response code: ' . json_encode($request));
                }
            } catch (\Exception $e) {
                $this->_logger->error('API Auth token refresh failed: ' . $e->getMessage());
            }
            sleep(1);
            $a++;
        }
        return $success;
    }
}
