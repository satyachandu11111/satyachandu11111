<?php

namespace Homescapes\EmailVerificationApi\Observer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class VerificationResponse implements \Magento\Framework\Event\ObserverInterface
{
	protected $client;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->scopeConfig = $scopeConfig;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$displayEmail = $observer->getData('email');
        $valid = $observer->getData('valid');

		$url = $this->scopeConfig->getValue('email_verification/demo/url');
        $path = $this->scopeConfig->getValue('email_verification/demo/path');
        $username = $this->scopeConfig->getValue('email_verification/demo/username');
        $password = $this->scopeConfig->getValue('email_verification/demo/password');

		$this->client = new Client(['base_uri' => $url]);
        try {
            $response = $this->client->request('POST', $path, [
                'auth' => [
                    $username,
                    $password
                ],'json' => ['username' => $displayEmail,'valid'=>$valid]
            ]);

            //$response = str_replace('"', '', $response->getBody()->getContents());
            return $response->getBody()->getContents();
        }
        catch (GuzzleException $e) {
            //echo $e->getRequest();
            //if ($e->hasResponse()) echo $e->getResponse();
            throw $e;
        }
        catch (\Exception $e) {
            //echo $e->getRequest();
            //if ($e->hasResponse()) echo $e->getResponse();
            throw $e;
        }
	}
}