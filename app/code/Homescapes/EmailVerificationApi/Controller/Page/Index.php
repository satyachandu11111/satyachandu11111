<?php

namespace Homescapes\EmailVerificationApi\Controller\Page;

use Homescapes\EmailVerificationApi\Model\Data\EmailVerification;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Homescapes\EmailVerificationApi\Model\Data\EmailVerificationFactory;
use Homescapes\EmailVerificationApi\Helper\Email;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends Action
{
    protected $pageFactory;

    protected $_emailVerificationFactory;

    protected $helperEmail;

    protected $_scopeConfig;

    public function __construct(
        ResultFactory $resultFactory,
        Context $context,
        EmailVerificationFactory $emailVerificationFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
        Email $helperEmail,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->resultFactory = $resultFactory;
        $this->_emailVerificationFactory = $emailVerificationFactory;
        $this->_forwardFactory = $forwardFactory;
        $this->helperEmail = $helperEmail;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $storeName = $this->_scopeConfig->getValue('general/store_information/name');
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->getConfig()->getTitle()->set('Store Connection with '.$storeName);
        $block = $page->getLayout()->getBlock('emailverification_page_index');

        $code = $this->getRequest()->getparam("code");
        if (!empty($code)) {
            $approval = $this->_emailVerificationFactory->create()->getCollection()->addFieldToFilter('verification_code', $code);
            $approval->getSelect()->limit(1);
            if($approval->count() > 0) {
                /**
                 * @var  $key
                 * @var EmailVerification $row
                 */
                foreach ($approval as $key => $row) {
                    //echo "<pre>";print_r($row->getData());exit;
                    //if ($row->getStatus() == 0) {
                        try {
                            $valid = true;
                            $this->_eventManager->dispatch('emailverification_send_response', ['email' => $row->getEmail(), 'valid' => $valid]);
                            
                            $row->setStatus(true);
                            $row->setVerificationCode(NULL);
                            $row->save();

                            $message = __('You have successfully connected '.$storeName.' with Shopping Manager app.');
                            $block->setData('custom_parameter', $message);

                            //$this->_eventManager->dispatch('new_customer_verification_send_response', ['email' => $row->getEmail()]);
                            $this->_eventManager->dispatch('new_customer_verification_send_response', ['data_object' => $row]);
                            $emailVerified = true;
                            $this->helperEmail->sendEmail($row, $valid, $emailVerified);
                            break;

                        } catch (\Exception $e) {
                            $message = __('There is some issue in connecting to shopping manager application. Please contact to admin.');
                            $block->setData('custom_parameter', $message);
                        }
                    /*} else {
                        $message = __('Email has already been verified before.');
                        $block->setData('custom_parameter', $message);
                    }*/
                }
            } else {
                $message = __('Verification link has been expired.');
                $block->setData('custom_parameter', $message);
            }
        } else {
            $message = __('Verification link has been expired.');
            $block->setData('custom_parameter', $message);
        }

        return $page;
    }
}