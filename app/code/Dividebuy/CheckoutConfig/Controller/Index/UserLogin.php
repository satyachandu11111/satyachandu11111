<?php

namespace Dividebuy\CheckoutConfig\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;

class UserLogin extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $_customerAccountManagement;

    /**
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CustomerSession                            $customerSession
     * @param AccountManagementInterface                 $customerAccountManagement
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CustomerSession $customerSession,
        AccountManagementInterface $customerAccountManagement
    ) {
        $this->_resultPageFactory         = $resultPageFactory;
        $this->_customerSession           = $customerSession;
        $this->_customerAccountManagement = $customerAccountManagement;
        parent::__construct($context);
    }

    /**
     * Check the user credentials and generates session based on it
     * 
     * @return mixed
     */
    public function execute()
    {
        // Getting value of email and password enter by user.
        $email    = $this->getRequest()->getParam('userEmail');
        $password = $this->getRequest()->getParam('userPassword');

        // Check if entered credentials are correct.
        try {
            $customer = $this->_customerAccountManagement->authenticate($email, $password);
            $this->_customerSession->setCustomerDataAsLoggedIn($customer);
            $this->_customerSession->regenerateId();

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('checkoutconfig/index/continuetocheckout');
            return $resultRedirect;

        } catch (\Exception $ex) {
            return false;
        }
    }
}
