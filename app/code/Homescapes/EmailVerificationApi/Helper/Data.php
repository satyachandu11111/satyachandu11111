<?php
namespace Homescapes\EmailVerificationApi\Helper;

use Magento\Framework\App\Helper\Context;
use Homescapes\EmailVerificationApi\Model\Data\EmailVerificationFactory;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var EmailVerificationFactory
     */
    protected $_emailVerificationFactory;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        EmailVerificationFactory $emailVerificationFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->_emailVerificationFactory = $emailVerificationFactory;
    }

    public function getVerifiedCustomer()
    {
        $email = $this->customerSession->getCustomer()->getEmail();
        $model = $this->_emailVerificationFactory->create()->getCollection()->addFieldToFilter('email', $email)->addFieldToFilter('status', 1);
        $model->getSelect()->limit(1);
        return $model; 
    }
}