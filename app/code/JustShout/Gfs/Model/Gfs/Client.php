<?php

namespace JustShout\Gfs\Model\Gfs;

use GuzzleHttp\Client as GuzzleClient;
use JustShout\Gfs\Helper\Config;
use JustShout\Gfs\Logger\Logger;
use JustShout\Gfs\Model\Gfs\Cookie\AccessToken;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Gfs Client
 *
 * @package   JustShout\Gfs
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2018
 */
class Client
{
    /**
     * Gfs Identity Server Url
     */
    const GFS_IDENTITY_URL = 'https://identity.justshoutgfs.com/';

    /**
     * Gfs Result Url
     */
    const GFS_REST_URL = 'http://rest-checkout.justshoutgfs.com/';

    /**
     * Config Helper
     *
     * @var Config
     */
    protected $_config;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $_logger;

    /**
     * Identity Client
     *
     * @var GuzzleClient
     */
    protected $_identityClient;

    /**
     * Rest Client
     *
     * @var GuzzleClient
     */
    protected $_restClient;

    /**
     * Access Token Factory
     *
     * @var AccessToken
     */
    protected $_accessToken;

    /**
     * Json
     *
     * @var Json
     */
    protected $_json;

    /**
     * Client constructor
     *
     * @param Config      $config
     * @param Logger      $logger
     * @param AccessToken $accessToken
     * @param Json        $json
     */
    public function __construct(
        Config      $config,
        Logger      $logger,
        AccessToken $accessToken,
        Json        $json
    ) {
        $this->_config = $config;
        $this->_logger = $logger;
        $this->_accessToken = $accessToken;
        $this->_json = $json;
        $this->_identityClient = new GuzzleClient([
            'base_uri' => self::GFS_IDENTITY_URL
        ]);
        $this->_restClient = new GuzzleClient([
            'base_uri' => self::GFS_REST_URL
        ]);
    }

    /**
     * Get the access token for a client
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        if (!$this->_accessToken->get()) {
            try {
                $response = $this->_identityClient->request('POST', 'connect/token', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'client_id'     => $this->_config->getRetailerId(),
                        'client_secret' => $this->_config->getRetailerSecret(),
                        'grant_type'    => 'client_credentials',
                        'scope'         => 'read checkout-api',
                    ]
                ]);
                $data = $this->_json->unserialize($response->getBody());
                $accessTokenString = isset($data['access_token']) ? $data['access_token'] : null;
                if (!$accessTokenString) {
                    throw new \Exception('Access token not available. Please check your credentials.');
                }

                $accessTokenString = base64_encode($accessTokenString);
                $this->_accessToken->set($accessTokenString);

                return $accessTokenString;
            } catch (\Exception $e) {
                $this->_logger->debug($e->getMessage());
                $this->_accessToken->delete();
            }
        }

        return $this->_accessToken->get();
    }

    /**
     * Delete Access Token
     *
     * @return void
     */
    public function deleteAccessToken()
    {
        $this->_accessToken->delete();
    }

    /**
     * This method will perform the close checkout request to the GFS api, and it will retrieve the shipment details
     * to store against the order.
     *
     * @param string $sessionId
     * @param string $checkoutData
     *
     * @return array
     */
    public function closeCheckout($sessionId, $checkoutData)
    {
        $sessionId = base64_decode($sessionId);
        $accessToken = base64_decode($this->getAccessToken());
        $checkoutData = $this->_json->unserialize(base64_decode($checkoutData));

        $response = $this->_restClient->request('PATCH', 'api/CheckoutSession?sessionId=' . $sessionId, [
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ],
            'json' => $checkoutData,
        ]);

        $data = $this->_json->unserialize($response->getBody());

        return $data;
    }
}
