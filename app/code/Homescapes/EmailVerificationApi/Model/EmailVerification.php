<?php 
namespace Homescapes\EmailVerificationApi\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Homescapes\EmailVerificationApi\Model\Data\EmailVerificationFactory;
use Homescapes\EmailVerificationApi\Helper\Email;
use Magento\Framework\Webapi\Exception;
 
class EmailVerification {

	/**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
 
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $_orderFactory;

    protected $_emailVerificationFactory;

    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    protected $date;

    protected $_scopeConfig;

    /**
     * Data constructor.
     *
     * @param AccountManagementInterface $customerAccountManagement
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        EmailVerificationFactory $emailVerificationFactory,
        Email $helperEmail,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
        $this->_orderFactory = $orderFactory;
        $this->_emailVerificationFactory = $emailVerificationFactory;
        $this->messageManager = $messageManager;
        $this->helperEmail = $helperEmail;
        $this->response = $response;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->date = $date;
        $this->_scopeConfig = $scopeConfig;
    }

	/** 
	 * @api 
	 * @param string $email
	 * @param bool $valid 
	 * @return string 
	 */
	public function getEmail($email,$valid = true)
	{
		if (!empty($email)) {
			$emailNotAvailable = $this->checkEmailExists($email);
			if ($emailNotAvailable) {
				//Retrieve all orders with this email address
				$orders = $this->_orderFactory->create()
					->getCollection()
					->addFieldToFilter('customer_email', $email);

				//Retrieve all order ids
				$orderCount = $orders->count();
				if ($orderCount > 0) {
					$emailNotAvailable = false;
				} else {
					throw new Exception(__('Not a valid email'));
				}
			}
            if(!$emailNotAvailable) return $this->storeData($email,$valid);
		}
        throw new Exception(__('Email is empty'));
	}

	private function checkEmailExists($email) {
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
        return $isEmailNotExists;
	}


	private function storeData($email,$valid) {
		$msg = array();
		$result = $this->jsonResultFactory->create();
		$newActivation = $this->_emailVerificationFactory->create()->getCollection()->addFieldToFilter('email', $email);
		$verificationCode = md5(uniqid(rand(), true));
        $currentTime = $this->date->gmtDate();
		if($newActivation->count() == 0) {
		    /** @var \Homescapes\EmailVerificationApi\Model\Data\EmailVerification $model */
			$model = $this->_emailVerificationFactory->create();
			$model->setEmail($email);
		} else {
			$newActivationFirstItem = $newActivation->getFirstItem();
			$model = $this->_emailVerificationFactory->create()->load($newActivationFirstItem->getCustomerId());
		}
        if($model){
            if($valid){
                /*if($model->getStatus())
                {
                    throw new Exception(__('Email has already been verified before.'));
                    return json_encode($msg);
                }*/
                $model->setVerificationCode($verificationCode);
                $msg['response'] = 'Verification Link has been sent to email id';
                $createdAt = $model->getUpdatedAt();
                $timeInterval = $this->_scopeConfig->getValue('email_verification/demo/time_interval');
                $sendEmailRestrictTime = date('Y-m-d H:i:s', strtotime($createdAt. '+'.$timeInterval.' minutes'));
                //var_dump($createdAt);exit;
                if((strtotime($currentTime) < strtotime($sendEmailRestrictTime)) && ($model->getCreatedAt()))
                {
                    throw new Exception(__('Link has already been sent to email id previously'));
                    return json_encode($msg);
                }
            }
            else {
                $model->setVerificationCode(null);
                $model->setStatus(false);
                $msg['response'] = 'You have disconneted from the app.';
            }
            //$model->setCreatedAt($currentTime);
            $model->setUpdatedAt($currentTime);
            $model->setStoreId($this->storeManager->getStore()->getId());
            if($model->save()){
                try {
                    $this->helperEmail->sendEmail($model, $valid);
                    return json_encode($msg);
                } catch (\Exception $e) {
                    throw new Exception(__('Cannot save product.'));
                }
            } else {
                throw new Exception(__('Problem in data store'));
            }
        }
	}
}