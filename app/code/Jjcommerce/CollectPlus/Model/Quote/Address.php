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


/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Jjcommerce\CollectPlus\Model\Quote;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Sales Quote address model
 *
 * @method int getQuoteId()
 * @method Address setQuoteId(int $value)
 * @method string getCreatedAt()
 * @method Address setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Address setUpdatedAt(string $value)
 * @method \Magento\Customer\Api\Data\AddressInterface getCustomerAddress()
 * @method Address setCustomerAddressData(\Magento\Customer\Api\Data\AddressInterface $value)
 * @method string getAddressType()
 * @method Address setAddressType(string $value)
 * @method int getFreeShipping()
 * @method Address setFreeShipping(int $value)
 * @method int getCollectShippingRates()
 * @method Address setCollectShippingRates(int $value)
 * @method Address setShippingMethod(string $value)
 * @method string getShippingDescription()
 * @method Address setShippingDescription(string $value)
 * @method float getWeight()
 * @method Address setWeight(float $value)
 * @method float getSubtotal()
 * @method Address setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method Address setBaseSubtotal(float $value)
 * @method Address setSubtotalWithDiscount(float $value)
 * @method Address setBaseSubtotalWithDiscount(float $value)
 * @method float getTaxAmount()
 * @method Address setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method Address setBaseTaxAmount(float $value)
 * @method float getShippingAmount()
 * @method float getBaseShippingAmount()
 * @method float getShippingTaxAmount()
 * @method Address setShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method Address setBaseShippingTaxAmount(float $value)
 * @method float getDiscountAmount()
 * @method Address setDiscountAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method Address setBaseDiscountAmount(float $value)
 * @method float getGrandTotal()
 * @method Address setGrandTotal(float $value)
 * @method float getBaseGrandTotal()
 * @method Address setBaseGrandTotal(float $value)
 * @method string getCustomerNotes()
 * @method Address setCustomerNotes(string $value)
 * @method string getDiscountDescription()
 * @method Address setDiscountDescription(string $value)
 * @method null|array getDiscountDescriptionArray()
 * @method Address setDiscountDescriptionArray(array $value)
 * @method float getShippingDiscountAmount()
 * @method Address setShippingDiscountAmount(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method Address setBaseShippingDiscountAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method Address setSubtotalInclTax(float $value)
 * @method float getBaseSubtotalTotalInclTax()
 * @method Address setBaseSubtotalTotalInclTax(float $value)
 * @method int getGiftMessageId()
 * @method Address setGiftMessageId(int $value)
 * @method float getDiscountTaxCompensationAmount()
 * @method Address setDiscountTaxCompensationAmount(float $value)
 * @method float getBaseDiscountTaxCompensationAmount()
 * @method Address setBaseDiscountTaxCompensationAmount(float $value)
 * @method float getShippingDiscountTaxCompensationAmount()
 * @method Address setShippingDiscountTaxCompensationAmount(float $value)
 * @method float getBaseShippingDiscountTaxCompensationAmnt()
 * @method Address setBaseShippingDiscountTaxCompensationAmnt(float $value)
 * @method float getShippingInclTax()
 * @method Address setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method \Magento\SalesRule\Model\Rule[] getCartFixedRules()
 * @method int[] getAppliedRuleIds()
 * @method Address setBaseShippingInclTax(float $value)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Address extends \Magento\Quote\Model\Quote\Address
{

    /**
     * Retrieve all grouped shipping rates
     *
     * @return array
     */
    public function getGroupedAllShippingRates()
    {
        $rates = [];
        $collectSortOrders = $this->_scopeConfig->getValue('carriers/collect/sort_methods');
        $collectSortOrders = explode(',', $collectSortOrders);
        $_collectSortOrders = array();
        foreach($collectSortOrders as $sortNo) {
            switch($sortNo) {
                case '24' :
                    $_collectSortOrders[] = 'collect_next';break;
                case '48' :
                    $_collectSortOrders[] = 'collect_48hr';break;
                case '72' :
                    $_collectSortOrders[] = 'collect_72hr';break;
                default   : $_collectSortOrders = array();
            }
        }
        $_collectSortOrders = array_flip($_collectSortOrders);
        $_collectSortOrders = is_array($_collectSortOrders) ? $_collectSortOrders : array();
        $flag = false;
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $this->_carrierFactory->get($rate->getCarrier())) {
                if($this->getQuote()->getAgentData()) {
                    //$pos = strpos($rate->getCode(), 'collect_collect');
                    $pos = strpos($rate->getCode(), 'collect_');
                    $poserror = strpos($rate->getCode(), 'collect_error');
                    if($pos === false) {
                        continue;
                    }
                    if (!isset($rates[$rate->getCarrier()])) {
                        $rates[$rate->getCarrier()] = [];
                    }
                    if(!empty($_collectSortOrders)) {
                        $posMothod = isset($_collectSortOrders[$rate->getMethod()]) ? $_collectSortOrders[$rate->getMethod()] : rand(100,200);
                        $rates[$rate->getCarrier()][$posMothod] = $rate;
                    } else {
                        $rates[$rate->getCarrier()][] = $rate;
                    }
                    ksort($rates[$rate->getCarrier()]);
                    $flag = true;
                } else {
                    if (!isset($rates[$rate->getCarrier()])) {
                        $rates[$rate->getCarrier()] = [];
                    }

                    $rates[$rate->getCarrier()][] = $rate;
                    $rates[$rate->getCarrier()][0]->carrier_sort_order = $this->_carrierFactory->get(
                        $rate->getCarrier()
                    )->getSortOrder();
                }
            }
        }
        uasort($rates, [$this, '_sortRates']);

        return $rates;
    }

}
