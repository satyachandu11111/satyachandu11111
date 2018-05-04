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


namespace Jjcommerce\CollectPlus\Model\Checkout\Type;


class Onepage extends \Magento\Checkout\Model\Type\Onepage
{


    protected $_collecthelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;


    /**
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Jjcommerce\CollectPlus\Helper\Data $_collecthelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_collecthelper = $_collecthelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
    }

    public function saveSmsNumber($data)
    {
        if ($this->getQuote()->getId()) {
            if (isset($data['mob']) && $data['mob'] !='') {
                $this->getQuote()->setSmsAlert($data['mob']);
                //$this->getQuote()->collectTotals();
                $this->getQuote()->save();
            } else {
                $this->getQuote()->setSmsAlert('');
                $this->getQuote()->save();
            }
        }
        return array();
    }


    public function saveAgent($data)
    {
        if ($this->getQuote()->getId()) {
            if (isset($data['agent_id']) && $data['agent_id'] !='' && $data['agent_id'] !=0) {
                $agent = $this->_collecthelper->getAgentInformation($data['agent_id'], 1, 1);

                if (count($agent) > 0) {
                    $this->getQuote()->setData('agent_data', serialize($agent));
                } else {
                    $this->getQuote()->setData('agent_data', '');
                }
//          $this->getQuote()->collectTotals();
                $this->getQuote()->save();
            } else {
                $this->getQuote()->setAgentData('');
                $this->getQuote()->save();
            }
        }
        return array();
    }
}
