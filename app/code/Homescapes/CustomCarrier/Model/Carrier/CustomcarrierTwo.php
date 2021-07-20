<?php

namespace Homescapes\CustomCarrier\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

/**
 * Custom shipping model
 */
class CustomcarrierTwo extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'dpd';


    /**
     * Custom Shipping Rates Collector
     *
     */
    public function collectRates(RateRequest $request)
    {
        return false;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }


     /**
     * Check if carrier has shipping tracking option available
     *
     * All \Magento\Usa carriers have shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }
}
