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

use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Aheadworks\Raf\Controller\AdvocateAction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CreateLink
 *
 * @package Aheadworks\Raf\Controller\Advocate
 */
class CreateLink extends AdvocateAction
{
    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param AdvocateManagementInterface $advocateManagement
     * @param StoreManagerInterface $storeManager
     * @param FormKeyValidator $formKeyValidator
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        AdvocateManagementInterface $advocateManagement,
        StoreManagerInterface $storeManager,
        FormKeyValidator $formKeyValidator
    ) {
        parent::__construct($context, $customerSession, $storeManager, $advocateManagement);
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($this->isValid()) {
                $this->advocateManagement->createReferralLink(
                    $this->customerSession->getCustomerId(),
                    $this->storeManager->getWebsite()->getId()
                );
                $this->messageManager->addSuccessMessage(__('Referral link has been successfully created.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while creating the referral link.')
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
}
