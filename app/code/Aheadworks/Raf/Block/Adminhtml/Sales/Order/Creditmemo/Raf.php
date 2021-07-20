<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Block\Adminhtml\Sales\Order\Creditmemo;

use Aheadworks\Raf\Model\Config;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Helper\Admin;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;

/**
 * Class Raf
 *
 * @package Aheadworks\Raf\Block\Adminhtml\Sales\Order\Creditmemo
 */
class Raf extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Raf::sales/order/creditmemo/raf.phtml';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Admin
     */
    private $adminHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->adminHelper = $adminHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve credit memo
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->coreRegistry->registry('current_creditmemo');
    }

    /**
     * Check whether can refund reward points to customer
     *
     * @return bool
     */
    public function canRefund()
    {
        $order = $this->getCreditmemo()->getOrder();
        $creditmemo = $this->getCreditmemo();

        return empty($order->getAwRafIsFriendDiscount())
            && abs($creditmemo->getBaseAwRafAmount()) && $this->isRefundOffline();
    }

    /**
     * Retrieve value to refund on RAF
     *
     * @return string
     */
    public function getRefundToRaf()
    {
        $order = $this->getCreditmemo()->getOrder();
        if ($order->getAwRafAmountType() == AdvocateOffType::PERCENT) {
            return abs($order->getAwRafPercentAmount()) . '%';
        } else {
            return $this->adminHelper->displayPrices(
                $this->getCreditmemo()->getOrder(),
                abs($this->getCreditmemo()->getBaseAwRafAmount()),
                abs($this->getCreditmemo()->getAwRafAmount()),
                true,
                ' '
            );
        }
    }

    /**
     * Check that is offline refund or not
     *
     * @return bool
     */
    private function isRefundOffline()
    {
        if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
            return false;
        }
        return true;
    }
}
