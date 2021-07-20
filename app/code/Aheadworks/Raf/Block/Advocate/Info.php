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
namespace Aheadworks\Raf\Block\Advocate;

use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Raf\Model\Advocate\Account\Viewer as AdvocateAccountViewer;
use Aheadworks\Raf\Model\Advocate\Url as AdvocateUrl;

/**
 * Class Info
 *
 * @package Aheadworks\Raf\Block\Advocate
 */
class Info extends Template
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var AdvocateAccountViewer
     */
    private $advocateAccountViewer;

    /**
     * @var AdvocateManagementInterface
     */
    private $advocateManagement;

    /**
     * @var AdvocateUrl
     */
    private $advocateUrl;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param AdvocateAccountViewer $advocateAccountViewer
     * @param AdvocateManagementInterface $advocateManagement
     * @param AdvocateUrl $advocateUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        AdvocateManagementInterface $advocateManagement,
        AdvocateAccountViewer $advocateAccountViewer,
        AdvocateUrl $advocateUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->advocateManagement = $advocateManagement;
        $this->advocateAccountViewer = $advocateAccountViewer;
        $this->advocateUrl = $advocateUrl;
    }

    /**
     * Retrieve create referral link url
     *
     * @return string
     */
    public function getCreateReferralLinkUrl()
    {
        return $this->advocateUrl->getCreateReferralLinkUrl();
    }

    /**
     * Check if available create referral link
     *
     * @return bool
     */
    public function isAvailableCreateReferralLink()
    {
        list($customerId, $websiteId) = $this->getCustomerIdAndWebsiteId();
        return !$this->advocateManagement->isParticipantOfReferralProgram($customerId, $websiteId);
    }

    /**
     * Retrieve advocate cumulative balance
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdvocateCumulativeBalance()
    {
        list($customerId, $store) = $this->getCustomerIdAndStore();
        return $this->advocateAccountViewer->getCumulativeBalanceFormatted($customerId, $store->getId());
    }

    /**
     * Retrieve advocate balance expired
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdvocateBalanceExpired()
    {
        list($customerId, $store) = $this->getCustomerIdAndStore();
        return $this->advocateAccountViewer->getBalanceExpiredFormatted($customerId, $store->getId());
    }

    /**
     * Retrieve advocate cumulative balance
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdvocateInvitedFriendsCount()
    {
        list($customerId, $websiteId) = $this->getCustomerIdAndWebsiteId();
        return $this->advocateAccountViewer->getInvitedFriendsCount($customerId, $websiteId);
    }

    /**
     * Retrieve customer id and website id
     *
     * @return array
     */
    public function getCustomerIdAndWebsiteId()
    {
        list($customerId, $store) = $this->getCustomerIdAndStore();
        return [$customerId, $store->getWebsiteId()];
    }

    /**
     * Get advocate reward message
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getRewardMessage()
    {
        list($customerId, $websiteId) = $this->getCustomerIdAndWebsiteId();
        return $this->advocateAccountViewer->getRewardMessage($customerId, $websiteId);
    }

    /**
     * Check if invitation section is visible
     *
     * @return bool
     */
    public function isInvitationSectionVisible()
    {
        list($customerId, $websiteId) = $this->getCustomerIdAndWebsiteId();
        return $this->advocateManagement->canParticipateInReferralProgram($customerId, $websiteId);
    }

    /**
     * Retrieve customer id and store
     *
     * @return array
     */
    private function getCustomerIdAndStore()
    {
        return [$this->customerSession->getCustomerId(), $this->_storeManager->getStore()];
    }
}
