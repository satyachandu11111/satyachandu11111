<?php
namespace HubBox\HubBox\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

use HubBox\HubBox\Model\QuoteFactory;
use HubBox\HubBox\Logger\Logger;

use Magento\Checkout\Model\Session as CheckoutSession;

class PrivateCollect extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    const METHOD_CODE = 'privatecollect';

    protected $_code = 'privatecollect';

    protected $_checkoutSession;

    protected $_hubBoxQuote;

    /** @var  Logger $logger */
    protected $_logger;

    /**
     * PrivateCollect constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param CheckoutSession $checkoutSession
     * @param QuoteFactory $hubBoxQuote
     * @param Logger $hblogger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        CheckoutSession $checkoutSession,
        QuoteFactory $hubBoxQuote,
        Logger $hblogger,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;

        $this->_checkoutSession = $checkoutSession;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_logger = $hblogger;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['privatecollect' => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {

        $quoteId = $this->_checkoutSession->getQuote()->getId();
        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quoteId, 'quote_id');

        // don't show if not private cp
        if (!$hubBoxQuote->getId()) {
            return false;
        }
        if ($hubBoxQuote->getCollectPointType() == 'hubbox') {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier('privatecollect');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('privatecollect');
        $method->setMethodTitle($this->getConfigData('name'));

        $amount = $this->getConfigData('price');
        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }
}