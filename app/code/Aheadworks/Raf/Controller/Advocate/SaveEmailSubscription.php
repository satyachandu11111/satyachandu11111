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
namespace Aheadworks\Raf\Controller\Advocate;

use Aheadworks\Raf\Controller\AdvocateAction;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Source\SubscriptionStatus;

/**
 * Class SaveEmailSubscription
 * @package Aheadworks\Raf\Controller\Advocate
 */
class SaveEmailSubscription extends AdvocateAction
{
    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param AdvocateManagementInterface $advocateManagement
     * @param StoreManagerInterface $storeManager
     * @param FormKeyValidator $formKeyValidator
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        AdvocateManagementInterface $advocateManagement,
        StoreManagerInterface $storeManager,
        FormKeyValidator $formKeyValidator,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository
    ) {
        parent::__construct($context, $customerSession, $storeManager, $advocateManagement);
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Perform save subscription action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($this->isValid()) {
                $isSubscribed = (boolean)$this->getRequest()->getParam('aw_raf_is_new_reward_notification_subscribed');
                $this->performSaveSubscription($isSubscribed);
                $this->messageManager->addSuccessMessage(__('Email notification settings were updated.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving notification settings.')
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Is valid request
     *
     * @return bool
     * @throws LocalizedException
     */
    private function isValid()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Invalid Form Key.'));
        }
        return true;
    }

    /**
     * Perform save subscription
     *
     * @param bool $isSubscribed
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function performSaveSubscription($isSubscribed)
    {
        $subscriptionStatus = $isSubscribed
            ? SubscriptionStatus::SUBSCRIBED
            : SubscriptionStatus::NOT_SUBSCRIBED;
        $customerId = $this->customerSession->getCustomerId();
        $websiteId = $this->storeManager->getWebsite()->getId();

        $this->advocateManagement->updateNewRewardSubscriptionStatus($customerId, $websiteId, $subscriptionStatus);
    }

    /**
     * Get advocate entry by customer ID
     *
     * @param $websiteId
     * @param $customerId
     * @return AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAdvocateByCustomerId($customerId, $websiteId)
    {
        return $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
    }
}
