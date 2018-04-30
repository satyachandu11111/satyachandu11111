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

namespace Jjcommerce\CollectPlus\Controller\Index;


class Getpostcode extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     *
     */
    protected $_jsonHelper;

    const XML_PATH_GOOGLE_API_KEY = 'carriers/collect/google_map_key';


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger //log injection

    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->_scopeConfig = $scopeConfig;

        parent::__construct(
            $context
        );
    }

    /**
     * Get postcode by lattitude and longitude
     *
     * @return string
     */

    public function execute()
    {

        $lat = $this->getRequest()->getParam('lattitude');
        $lng = $this->getRequest()->getParam('longitude');

        $returnValue = NULL;
        $ch = curl_init();
        $googleApiKey = $this->getGoogleApiKey();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=$googleApiKey&latlng=${lat},${lng}";
        $this->logger->debug($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        try {
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            //echo $e->getMessage();
            $this->logger->critical($e);
        }
        $json = json_decode($result, TRUE);
        if (isset($json['results'])) {
            foreach ($json['results'] as $result) {
                foreach ($result['address_components'] as $address_component) {
                    $types = $address_component['types'];
                    if (in_array('postal_code', $types) && sizeof($types) == 1) {
                        $returnValue = $address_component['short_name'];
                        break;
                    }
                }
                if($returnValue) {
                    break;
                }
            }
        }
        return $this->getResponse()->setBody($this->_jsonHelper->jsonEncode(array('postcode' => str_ireplace(" ", "",$returnValue))));

    }

    /**
     * @return null|string
     */
    public function getGoogleApiKey() {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GOOGLE_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
