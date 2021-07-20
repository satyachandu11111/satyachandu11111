<?php

namespace Homescapes\ExpressShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Custom shipping model
 */
class Expressshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'expressshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var DateTime
     */
    protected $datetime;

    protected $serialize;

     /**
     * @var \Magento\Checkout\Model\Session|\Magento\Backend\Model\Session\Quote
     */
    protected $session;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        DateTime $datetime,
        TimezoneInterface $timezone,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magento\Framework\App\State $state,
        \Homescapes\Preorder\Helper\Data $helperExpress,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->timezone      = $timezone;
        $this->datetime      = $datetime;
        $this->serialize     = $serialize;
        $this->helperExpress = $helperExpress;
        $this->productRepository = $productRepository;


        if ($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $this->session = $backendQuoteSession;
        } else {
            $this->session = $checkoutSession;
        }
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
         $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/exprestriction.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('collectRates');

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        //Disabled if sku is not valid :
         /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->session->getQuote();
        $quoteItems = $quote->getAllItems();
       
        $heavyWeightFlag = false;
        $expresDelviery=false;
        foreach ($quoteItems as $item) {
            
            $logger->info('Disabled : '.$item->getProduct()->getExpShipRestriction());

            $productDetail = $this->productRepository->get($item->getProduct()->getSku());
            if($this->helperExpress->isPreOrder($productDetail)){
                return false;
            }
            if ($item->getProduct()->getExpShipRestriction()) {
                $heavyWeightFlag = true;
                continue;
            }
        }
       
        if ($heavyWeightFlag == true) {
            return false;
        }

        //END of SKU CODE : 


        $date                 = $this->timezone->date();
        $currentHours         = (int)$date->format('H');
        $currentMinutes       = (int)$date->format('i');
        $currentTimeInMinutes = $currentHours * 60 + $currentMinutes;

        $ruleDays = $this->getConfigData('week_days');
        $ruleDays = explode(',', $ruleDays);
       
        $currentStoreDate = $date->format('Y-m-d');
        $dayOfTheWeek = date('w',strtotime($currentStoreDate));  

        if (!in_array($dayOfTheWeek, $ruleDays)) {
            return false;
        }


        $holidays = $this->getOffDates();        

        // $currentHours * 60 + $currentMinutes
        //if($currentTimeInMinutes > 780) // means less then 1PM 
        //{
        //    return false;
        //}


        if(in_array($currentStoreDate, $holidays)) // check holidays
        {
            return false;
        }
        

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingCost = (float)$this->getConfigData('shipping_cost');

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);        
        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    // get excluded/ OFF days
    public function getOffDates() {

        $offDaysString = $ruleDays = $this->getConfigData('mapping');
        $Offdates =array();
            
           $offDaysString =  $this->serialize->unserialize($offDaysString);
           foreach ($offDaysString as $value) {
                
                $Offdates[]=$value['field2'];
                
           }    
        return $Offdates;     
    }

    
}
