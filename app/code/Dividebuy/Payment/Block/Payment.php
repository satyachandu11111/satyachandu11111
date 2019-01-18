<?php

namespace Dividebuy\Payment\Block;

use Dividebuy\CheckoutConfig\Block\Cart as CheckoutConfigBlock;
use Magento\Framework\View\Element\Template\Context;
USE Dividebuy\RetailerConfig\Helper\Data AS RetailerHelper;

class Payment extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CheckoutConfigBlock
     */
    protected $_checkoutConfigBlock;

    /**
     * @var \Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_retailerHelper;

    /**
     * @param Context             $context
     * @param CheckoutConfigBlock $checkoutConfigBlock
     * @param RetailerHelper $retailerHelper
     * @param array               $data
     */
    public function __construct(Context $context,
        CheckoutConfigBlock $checkoutConfigBlock,
        RetailerHelper $retailerHelper,
        array $data = []
    ) {
        $this->_checkoutConfigBlock = $checkoutConfigBlock;
        $this->_retailerHelper      = $retailerHelper;
        parent::__construct($context, $data);
    }

    /**
     * Returns an object of CheckoutConfigBlock
     * 
     * @return object
     */
    public function getCheckoutConfigBlock()
    {
        return $this->_checkoutConfigBlock;
    }

    /**
     * Return an instance of \Dividebuy\RetailerConfig\Helper\Data
     *
     * @return \Dividebuy\RetailerConfig\Helper\Data
     */
    public function getRetailerHelper()
    {
        return $this->_retailerHelper;
    }
}
