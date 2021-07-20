<?php

namespace HubBox\HubBox\Observer;

use HubBox\HubBox\Model\QuoteFactory;
use HubBox\HubBox\Logger\Logger;
use HubBox\HubBox\Helper\Checkout;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class Recalculate implements ObserverInterface
{

    protected $_request;
    protected $_checkoutSession;
    protected $_hubBoxQuote;
    protected $_helper;
    protected $_logger;

    /**
     * Recalculate constructor.
     * @param Http $request
     * @param CheckoutSession $checkoutSession
     * @param QuoteFactory $hubBoxQuote
     * @param Logger $logger
     * @param Checkout $helper
     */
    public function __construct(
        Http $request,
        CheckoutSession $checkoutSession,
        QuoteFactory $hubBoxQuote,
        Logger $logger,
        Checkout $helper
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_hubBoxQuote = $hubBoxQuote;
        $this->_request = $request;
        $this->_logger = $logger;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $quote          = $this->_checkoutSession->getQuote();
        $quoteId        = $quote->getId();
        $isHubBox       = $this->_request->getParam('isHubBox');
        $hubBoxQuote    = $this->_getHubBoxQuote($quoteId);

        if ($isHubBox == '1') {

            $collectPointId     = $this->_request->getParam('collectPointId');
            $collectPointType   = $this->_request->getParam('collectPointType');

            try {
                $data = [
                    'hubbox_collect_point_id' =>  $collectPointId,
                    'collect_point_type' =>  $collectPointType,
                ];
                $hubBoxQuote->addData($data)->save();
                $this->_helper->updateQuoteShipping($quote, $collectPointId);
                if ((intval($quoteId) > 0) && intval($collectPointId) > 0) {
                    $this->_logger->info('recalculate: #' . $quoteId . ' is HubBox order. CollectPoint: ' . $collectPointId);
                } else {
                    $this->_logger->info('recalculate: is HubBox order but missing quote id. CollectPoint: ' . ($collectPointId > 0) ? $collectPointId : 'Missing');
                }

            } catch (\Exception $e){
                $e->getMessage();
            }

        } else {

            try {
                $hubBoxQuote->delete();

                $this->_helper->noHubBox($quote);
                $this->_logger->info('recalculate: #' . (intval($quoteId) > 0 ? $quoteId : "Missing") . ' is not HubBox order');

            } catch (\Exception $e){
                $e->getMessage();
            }

        }
    }

    protected function _getHubBoxQuote($quoteId)
    {
        $hubBoxQuote = $this->_hubBoxQuote->create()->load($quoteId, 'quote_id');
        if ($hubBoxQuote->getQuoteId()) {
            return $hubBoxQuote;
        } else {
            $data = ['quote_id' =>  $quoteId];
            return $this->_hubBoxQuote->create()->addData($data);
        }
    }
}
