<?php 

namespace Homescapes\EmailVerificationApi\Controller\Customer;

use Homescapes\EmailVerificationApi\Model\Data\EmailVerificationFactory;
use Homescapes\EmailVerificationApi\Helper\Email;

class Disconnectapp extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    protected $customerSession;

    protected $_emailVerificationFactory;

    protected $date;
    
    protected $helperEmail;

    protected $_emailVerificationHelper;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        EmailVerificationFactory $emailVerificationFactory,
        \Homescapes\EmailVerificationApi\Helper\Data $emailVerificationHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        Email $helperEmail
    ) {
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_emailVerificationFactory = $emailVerificationFactory;
        $this->_emailVerificationHelper = $emailVerificationHelper;
        $this->date = $date;
        $this->helperEmail = $helperEmail;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn())
        {
            $customURL = $this->_url->getUrl('registration/customer/index');
            $this->customerSession->setBeforeAuthUrl($customURL);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('registration');
            return $resultRedirect;
        }
        else
        {
            $currentTime = $this->date->gmtDate();
            $verifiedCustomer = $this->_emailVerificationHelper->getVerifiedCustomer();
            if ($verifiedCustomer->getSize() > 0) {
                $verifiedCustomerFirstItem = $verifiedCustomer->getFirstItem();
                $model = $this->_emailVerificationFactory->create()->load($verifiedCustomerFirstItem->getCustomerId());
                if($model){
                    $model->setVerificationCode(null);
                    $model->setStatus(false);

                    $model->setCreatedAt($currentTime);
                    $model->setUpdatedAt($currentTime);

                    if($model->save()){
                        try {
                            $valid = false;
                            $this->_eventManager->dispatch('emailverification_send_response', ['email' => $model->getEmail(), 'valid' => $valid]);
                            $this->helperEmail->sendEmail($model, $valid);
                            $this->messageManager->addSuccess(__('You have disconneted from the app.'));
                            $this->_redirect('*/*/index');
                            return;
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                        }
                    } else {
                        $this->messageManager->addError('Problem in data store');
                    }

                }
            }
        }
    }

}