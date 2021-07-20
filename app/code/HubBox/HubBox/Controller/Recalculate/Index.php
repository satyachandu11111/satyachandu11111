<?php

namespace HubBox\HubBox\Controller\Recalculate;

use HubBox\HubBox\Api\CollectPoints;
use HubBox\HubBox\Logger\Logger;
use HubBox\HubBox\Model\QuoteFactory;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

class Index extends Action
{

    protected $_checkoutSession;
    protected $_logger;
    protected $_collectPoints;
    protected $_hubBoxQuote;
    protected $_request;
    protected $_resultJsonFactory;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        CollectPoints $collectPoints,
        QuoteFactory $hubBoxQuote,
        Logger $logger,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
        $this->_collectPoints = $collectPoints;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_logger = $logger;
        $this->_resultJsonFactory = $resultJsonFactory;
    }


    /**
     * Dispatch custom event
     */
    public function execute()
    {
        // call recalc event
        $this->_eventManager->dispatch('hubbox_recalculate');

        $quote = $this->_checkoutSession->getQuote();
        $quoteId = $quote->getId();
        $result = ['isHubBox' => false];

        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quoteId, 'quote_id');
        if ($hubBoxQuote->getQuoteId()) {
            $result['isHubBox'] = true;
        }

        $result = $this->_resultJsonFactory->create()->setData($result);
        return $result;
    }

}
