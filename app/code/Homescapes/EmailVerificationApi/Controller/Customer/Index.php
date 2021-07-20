<?php 

namespace Homescapes\EmailVerificationApi\Controller\Customer;  

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    protected $session;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
            if (!$this->session->isLoggedIn())
            {
                $customURL = $this->_url->getUrl('registration/customer/index');
                $this->session->setBeforeAuthUrl($customURL);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('registration');
                return $resultRedirect;
            }
            else
            {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__('Connected App'));
                return $resultPage;
            }
    }

}