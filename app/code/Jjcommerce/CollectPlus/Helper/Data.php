<?php

/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */

namespace Jjcommerce\CollectPlus\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_backendConfig;

    const XML_PATH_MODULE_ENABLED = 'carriers/collect/active';
    const XML_PATH_SMS_ALERT = 'carriers/collect/sms_alert';
    const XML_PATH_MAX_RESULT = 'carriers/collect/max_result';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Backend\App\ConfigInterface $backendConfig
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_backendConfig = $backendConfig;
        parent::__construct($context);
    }

    /**
     * Check whether module enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_MODULE_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return null|int
     */
    public function getMaxResult()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAX_RESULT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return null|int
     */
    public function canShowSmsBox() {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SMS_ALERT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    public function getCheckoutQuote()
    {
        $quote = $this->getCheckoutSession()->getQuote();

        return $quote;
    }

    /**
     * @return array
     */
    public function getPickupList($criteria, $searchType = 0)
    {
        $maxResult = $this->getMaxResult();
        $maxResult = $maxResult ? $maxResult : 20;

        $result = array();
        $agentList = array();
        $returnCode = '';
        $returnMessage = '';

        $url = "https://www.collectplus.co.uk/api/v1/agentlocator/default/AgentLocator.json?searchCriteria=$criteria&searchType=$searchType&maxRecords=$maxResult";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        try {
            $response = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            //echo $e->getMessage();
            $result['return_code'] = $returnCode;
            $result['return_message'] = $returnMessage;
            $result['agent_lists'] = $agentList;
            return $result;
        }
        $json = json_decode($response);

        if(!is_object($json)) {
            $result['return_code'] = $returnCode;
            $result['return_message'] = $returnMessage;
            $result['agent_lists'] = $agentList;
            return $result;
        }

        $returnCode = (string)$json->return_code;
        $returnMessage = (string)$json->return_message;

        $agents = $json->agents;

        foreach ($agents as $agent) {
            $array = array();
            foreach ($agent as $name => $item) {
                $name = str_ireplace(" ", "", ucwords(str_ireplace("_", " ", $name)));
                //hard fixes for HDNCode and DCLSiteName
                $name = $name == "HdnCode" ? "HDNCode" : $name;
                $name = $name == "DclSiteName" ? "DCLSiteName" : $name;
                $array[$name] = (string)$item;
            }
            $agentList[] = $array;
        }

        $result['return_code'] = $returnCode;
        $result['return_message'] = $returnMessage;
        $result['agent_lists'] = $agentList;

        return $result;
    }

    /**
     * @return array
     */
    public function getPickupList__old($criteria, $searchType = 0)
    {
        $maxResult = $this->getMaxResult();
        $maxResult = $maxResult ? $maxResult : 20;
        $soapUrl = "http://locator.paypoint.com:61001/AgentLocator.asmx?WSDL"; // asmx URL of WSDL

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                              <soap:Body>
                                <GetNearestAgentsType1 xmlns="http://paypoint.co.uk/">
                                 <searchCriteria>' . $criteria . '</searchCriteria>
                                  <searchType>' . $searchType . '</searchType>
                                  <maxRecords>' . $maxResult . '</maxRecords>
                                </GetNearestAgentsType1>
                              </soap:Body>
                            </soap:Envelope>';

        $headers = array(
            "POST /AgentLocator.asmx HTTP/1.1",
            "Host: locator.paypoint.com",
            "Content-Type: text/xml",
            "Content-Length: " . strlen($xml_post_string),
            "SOAPAction: http://paypoint.co.uk/GetNearestAgentsType1"
        ); //SOAPAction: your op URL
        $url = $soapUrl;
        // PHP cURL  for https connection with auth
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        try {

            $response = curl_exec($ch);
            curl_close($ch);

        } catch (Exception $e) {

            echo $e->getMessage();
        }


        //$doc = new DOMDocument();
        if(strlen($response)) {
            $doc = new \DOMDocument();
            $doc->loadXML($response);

            $returnCode = $doc->getElementsByTagName("ReturnCode")->item(0)->nodeValue;
            $returnMessage = $doc->getElementsByTagName("ReturnMessage")->item(0)->nodeValue;


            $soap = simplexml_load_string($response);
            $agents = $soap->children('http://schemas.xmlsoap.org/soap/envelope/')
                ->Body->children()
                ->GetNearestAgentsType1Response
                ->GetNearestAgentsType1Result
                ->Agents
                ->Agent;
        } else {
            $agents = array();
            $returnCode = '';
            $returnMessage = '';
        }

        $agentList = array();

        foreach ($agents as $agent) {
            $array = array();
            foreach ($agent as $item) {
                $array[$item->getName()] = (string)$item;
            }
            $agentList[] = $array;
        }

        $result = array();
        $result['return_code'] = $returnCode;
        $result['return_message'] = $returnMessage;
        $result['agent_lists'] = $agentList;

        return $result;

    }

    /**
     * @return array
     */
    public function getAgentInformation($criteria, $searchType, $maxResult)
    {
        $array = array();

        $url = "https://www.collectplus.co.uk/api/v1/agentlocator/default/AgentLocator.json?searchCriteria=$criteria&searchType=$searchType&maxRecords=$maxResult";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        try {
            $response = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            //echo $e->getMessage();
            return $array;
        }
        $json = json_decode($response);

        if(!is_object($json)) {

            return $array;
        }

        $agents = $json->agents;

        foreach ($agents as $agent) {
            $array = array();
            foreach ($agent as $name => $item) {
                $name = str_ireplace(" ", "", ucwords(str_ireplace("_", " ", $name)));
                //hard fixes for HDNCode and DCLSiteName
                $name = $name == "HdnCode" ? "HDNCode" : $name;
                $name = $name == "DclSiteName" ? "DCLSiteName" : $name;
                $array[$name] = (string)$item;
            }
        }

        return $array;
    }
    /**
     * @return array
     */
    public function getAgentInformation__old($criteria, $searchType, $maxResult)
    {

        $soapUrl = "http://locator.paypoint.com:61001/AgentLocator.asmx?WSDL"; // asmx URL of WSDL

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                              <soap:Body>
                                <GetNearestAgentsType1 xmlns="http://paypoint.co.uk/">
                                 <searchCriteria>' . $criteria . '</searchCriteria>
                                  <searchType>' . $searchType . '</searchType>
                                  <maxRecords>' . $maxResult . '</maxRecords>
                                </GetNearestAgentsType1>
                              </soap:Body>
                            </soap:Envelope>';

        $headers = array(
            "POST /AgentLocator.asmx HTTP/1.1",
            "Host: locator.paypoint.com",
            "Content-Type: text/xml",
            "Content-Length: " . strlen($xml_post_string),
            "SOAPAction: http://paypoint.co.uk/GetNearestAgentsType1"
        ); //SOAPAction: your op URL
        $url = $soapUrl;
        // PHP cURL  for https connection with auth
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        try {

            $response = curl_exec($ch);
            curl_close($ch);

        } catch (Exception $e) {

            echo $e->getMessage();
        }


        $doc = new \DOMDocument();
        $doc->loadXML($response);


        $returnCode = $doc->getElementsByTagName("ReturnCode")->item(0)->nodeValue;
        $returnMessage = $doc->getElementsByTagName("ReturnMessage")->item(0)->nodeValue;


        $soap = simplexml_load_string($response);
        $agents = $soap->children('http://schemas.xmlsoap.org/soap/envelope/')
            ->Body->children()
            ->GetNearestAgentsType1Response
            ->GetNearestAgentsType1Result
            ->Agents
            ->Agent;

        $_array = array();
        foreach ($agents as $agent) {
            $array = array();
            foreach ($agent as $item) {
                $_array[$item->getName()] = (string)$item;
            }
        }


        return $_array;
    }

}
