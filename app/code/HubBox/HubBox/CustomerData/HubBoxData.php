<?php

namespace HubBox\HubBox\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

use HubBox\HubBox\Model\QuoteFactory;
use HubBox\HubBox\Api\CollectPoints;

class HubBoxData extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    protected $_checkoutSession;
    protected $_hubBoxQuote;
    protected $_collectPoints;

    public function __construct(
        CheckoutSession $checkoutSession,
        QuoteFactory $hubBoxQuote,
        CollectPoints $collectPoints,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_checkoutSession = $checkoutSession;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_collectPoints = $collectPoints;
    }

    public function getSectionData()
    {
        $quote = $this->_checkoutSession->getQuote();
        $quoteId = $quote->getId();
        $result = ['collectPoint' => null];
        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quoteId, 'quote_id');
        $collectPointId = $hubBoxQuote->getData('hubbox_collect_point_id');

        if ($collectPointId) {
            $cp = $this->_collectPoints->getCollectPoint($collectPointId);
            $result = ['collectPoint' => $cp];
        }

        return $result;
    }
}