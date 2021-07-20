<?php

namespace HubBox\HubBox\Plugin\Magento\Quote\Model\Quote;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingMethodManagement;
use Magento\Checkout\Model\Session as CheckoutSession;


use HubBox\HubBox\Model\Carrier\PrivateCollect;
use HubBox\HubBox\Helper\Data;
use HubBox\HubBox\Model\QuoteAddressFactory;
use HubBox\HubBox\Logger\Logger as Logger;
use HubBox\HubBox\Model\QuoteFactory;

class ShippingMethodManagementPlugin
{

    protected $_checkoutSession;
    protected $_quoteAddress;
    protected $_hubBoxQuote;
    protected $_helper;
    protected $_logger;

    /**
     * ShippingMethodManagementPlugin constructor.
     * @param Data $helper
     * @param Logger $logger
     * @param QuoteFactory $hubBoxQuote
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Data $helper,
        Logger $logger,
        QuoteFactory $hubBoxQuote,
        CheckoutSession $checkoutSession,
        array $data = []
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_checkoutSession = $checkoutSession;
    }


    public function aroundEstimateByExtendedAddress(ShippingMethodManagement $subject,
                                                    \Closure $proceed, $cartId, AddressInterface $address) {

        $shippingMethods = $proceed($cartId, $address);

        $quote = $this->_checkoutSession->getQuote();
        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quote->getId(), 'quote_id');

        if ($hubBoxQuote->getId()) {
            if ($hubBoxQuote->getCollectPointType() !== 'hubbox') {
                foreach ($shippingMethods as $key => $shippingMethod) {
                    if ($shippingMethod->getMethodCode() !== PrivateCollect::METHOD_CODE) {
                        unset($shippingMethods[$key]);
                    }
                }
            }
        }

        return $shippingMethods;

    }
}