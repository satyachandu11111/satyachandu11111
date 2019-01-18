<?php
namespace Dividebuy\CheckoutConfig\Helper;

class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_divideBuylogger;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface            $logger
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Dividebuy\RetailerConfig\Logger\Logger $divideBuylogger
    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->_logger     = $logger;
        $this->_scopeConfig          = $scopeConfig;
        $this->_divideBuylogger         = $divideBuylogger;

    }

    /**
     * Used to send CURL request
     * 
     * @param  string $url
     * @param  array $params
     * @return array
     */
    public function sendRequest($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        try {
            $response = curl_exec($ch);
            $response = $this->_jsonHelper->jsonDecode($response);
            /*For error log code start*/
            $errorLogStatus = $this->_scopeConfig->getValue('dividebuy/general/allow_error_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,empty($storeId) ? 0 : $storeId);
            if($errorLogStatus == 1)
            {
                $dataResponse = "============\n";
                $dataResponse .= "Url :".$url." \n";
                $dataResponse .= "Param :".$params."\n";
                $dataResponse .= "Response :".$response."\n";
                $this->_divideBuylogger->info($dataResponse);    
            } 
            /*Error log code end*/
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }
        return $response; 
    }
}
