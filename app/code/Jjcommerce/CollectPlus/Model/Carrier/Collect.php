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


namespace Jjcommerce\CollectPlus\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

class Collect extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'collect';

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var array
     */
    protected $_conditionNames = [];

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_resultMethodFactory;

    protected $_collecthelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;



    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Jjcommerce\CollectPlus\Helper\Data $_collecthelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_collecthelper = $_collecthelper;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function collectRates(RateRequest $request)
    {

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $isAgentData = false;
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_checkoutSession->getQuote();

        if (is_object($quote) && $quote->getId() && $quote->getAgentData()) {
            $isAgentData = true;
        }

        $minAmount = $this->getConfigData('min_order_value');
        $maxAmount = $this->getConfigData('max_order_value');
        $minWeight = $this->getConfigData('min_order_weight');
        $maxWeight = $this->getConfigData('max_order_weight');

        $orderWeight = $request->getPackageWeight();


        if (($request->getBaseSubtotalInclTax() >= $minAmount && $request->getBaseSubtotalInclTax() <= $maxAmount) || (!$minAmount && !$maxAmount) || (!$minAmount && $maxAmount && $request->getBaseSubtotalInclTax() <= $maxAmount) || ($minAmount && !$maxAmount && $request->getBaseSubtotalInclTax() >= $minAmount)) {
            $show = true;
        } else {
            $show = false;
        }

        $showWeight = false;
        if(($orderWeight >= $minWeight && $orderWeight <= $maxWeight) || (!$minWeight && !$maxWeight) || (!$minWeight && $maxWeight && $orderWeight <= $maxWeight) || ($minWeight && !$maxWeight && $orderWeight >= $minWeight)) {
            $showWeight = true;
        }

        $show = $show && $showWeight && $isAgentData ? true : false;

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();

        if(!$show) {
            if($isAgentData) {
                $error = $this->_rateErrorFactory->create(
                    [
                        'data' => [
                            'carrier' => $this->_code,
                            'carrier_title' => $this->getConfigData('title'),
                            'error_message' => $this->getConfigData('errormsg'),
                        ],
                    ]
                );
                $result->append($error);
            }
            return $result;
        }

        $foundRates = false;
        $shippingMethods = array();

        $nextDay = $this->getConfigData('next_day');
        $nextTitle = $this->getConfigData('next_day_title');
        $nextCost = $this->getConfigData('next_day_price');
        $nextAccountNumber = $this->getConfigData('next_day_account');

        if ($nextDay && $nextAccountNumber) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('collect');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('collect_next');
            $method->setMethodTitle($nextTitle);

            $method->setPrice($nextCost);
            $method->setCost($nextCost);

            $shippingMethods[24] = $method;
        }

        $twoDay = $this->getConfigData('two_day');
        $twoTitle = $this->getConfigData('two_day_title');
        $twoCost = $this->getConfigData('two_day_price');
        $twoAccountNumber = $this->getConfigData('two_day_account');

        if ($twoDay && $twoAccountNumber) {
            //$result->append($method);

            $method2 = $this->_rateMethodFactory->create();

            $method2->setCarrier('collect');
            $method2->setCarrierTitle($this->getConfigData('title'));

            $method2->setMethod('collect_48hr');
            $method2->setMethodTitle($twoTitle);

            $method2->setPrice($twoCost);
            $method2->setCost($twoCost);

            $shippingMethods[48] = $method2;
        }

        $threeDay = $this->getConfigData('three_day');
        $threeTitle = $this->getConfigData('three_day_title');
        $threeCost = $this->getConfigData('three_day_price');
        $threeAccountNumber = $this->getConfigData('three_day_account');

        if ($threeDay && $threeAccountNumber) {
            //$result->append($method2);

            $method3 = $this->_rateMethodFactory->create();

            $method3->setCarrier('collect');
            $method3->setCarrierTitle($this->getConfigData('title'));

            $method3->setMethod('collect_72hr');
            $method3->setMethodTitle($threeTitle);

            $method3->setPrice($threeCost);
            $method3->setCost($threeCost);

            $shippingMethods[72] = $method3;
            //$result->append($method3);
        }
        $sortOrder = explode(',', $this->getConfigData('sort_methods'));
        $shippingMethodsSort = array();
        $diff = array_diff(array(24, 48, 72), $sortOrder);
        if (!empty($sortOrder) && empty($diff)) {
            foreach ($sortOrder as $sort) {
                if (isset($shippingMethods[$sort])) {
                    $shippingMethodsSort[] = $shippingMethods[$sort];
                }
            }
            if (!empty($shippingMethodsSort)) {
                //krsort($shippingMethodsSort);
                $shippingMethods = $shippingMethodsSort;
            }
        }
        foreach ($shippingMethods as $i => $shippingMethod) {
            $shippingMethod->setCarrierSortOrder($i + 1);
            $shippingMethod->setCollectSortOrder($i + 1);
            $result->append($shippingMethod);
            $foundRates = true; // have found some valid rates
        }

        if (!$foundRates){
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('errormsg'),
                    ],
                ]
            );
            $result->append($error);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['collect' => $this->getConfigData('name')];
    }
}
