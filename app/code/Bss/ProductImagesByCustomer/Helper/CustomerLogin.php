<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductImagesByCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductImagesByCustomer\Helper;

class CustomerLogin extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer Session
     * @var Session $customerSession
     */
    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * Check Customer Logined
     * @return bool
     */
    public function checkCustomerLogined()
    {
        if ($this->customerSession->create()->isLoggedIn()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get Email Customer
     * @return string
     */
    public function getEmailCustomer()
    {
        if ($this->checkCustomerLogined() == true) {
            return $this->customerSession->create()->getCustomer()->getEmail();
        } else {
            return null;
        }
    }

    /**
     * Get Email Customer
     * @return string
     */
    public function getNameCustomer()
    {
        if ($this->checkCustomerLogined() == true) {
            return $this->customerSession->create()->getCustomer()->getName();
        } else {
            return null;
        }
    }
}
