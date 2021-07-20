<?php
/**
 * A Magento 2 module named HubBox/HubBox
 * Copyright (C) 2017 HubBox Ltd
 *
 * This file is part of HubBox/HubBox.
 *
 * HubBox/HubBox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace HubBox\HubBox\Plugin\Magento\Quote\Model\Quote\Address\RateResult;

use HubBox\HubBox\Helper\Data;
use HubBox\HubBox\Model\QuoteAddressFactory;
use HubBox\HubBox\Logger\Logger as Logger;
use HubBox\HubBox\Model\QuoteFactory;

use \Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\QuoteRepository;

class MethodPlugin
{

    protected $_checkoutSession;
    protected $_quoteAddress;
    protected $_hubBoxQuote;

    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     */
    protected $_helper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /** @var  Logger $logger */
    protected $_logger;


    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param QuoteRepository $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param QuoteAddressFactory $quoteAddressFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        QuoteRepository $quoteRepository,
        CheckoutSession $checkoutSession,
        QuoteAddressFactory $quoteAddressFactory,
        Data $helper,
        Logger $logger,
        QuoteFactory $hubBoxQuote,
        array $data = []
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->_quoteRepository = $quoteRepository;
        $this->_quoteAddress = $quoteAddressFactory;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $subject
     * @param $price
     * @return $this
     */
    public function afterSetPrice(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $subject,
        $price
    )
    {
        $quote = $this->_checkoutSession->getQuote();
        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quote->getId(), 'quote_id');

        if ($hubBoxQuote->getId()) {
            if ($hubBoxQuote->getCollectPointType() == 'hubbox') {
                if ($quote->getBaseGrandTotal() >= $this->_helper->getBasketCutOff() && !$this->_helper->isFree()) {
                    $hubboxFee = $price->getPrice() + $this->_helper->addHigherFee();
                    $subject->setData('price', $this->_priceCurrency->round($hubboxFee));
                }

                if ($quote->getBaseGrandTotal() < $this->_helper->getBasketCutOff() && !$this->_helper->isFree()) {
                    $hubboxFee = $price->getPrice() + $this->_helper->addLowerFee();
                    $subject->setData('price', $this->_priceCurrency->round($hubboxFee));
                }
            }
        }

        return $this;
    }
}
