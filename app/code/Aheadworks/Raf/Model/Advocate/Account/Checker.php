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
namespace Aheadworks\Raf\Model\Advocate\Account;

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Raf\Model\Advocate\Account\Checker\CustomerGroup as CustomerGroupChecker;
use Aheadworks\Raf\Model\Advocate\Account\Checker\CustomerInvitation as CustomerInvitationChecker;
use Aheadworks\Raf\Api\AdvocateBalanceManagementInterface;
use Aheadworks\Raf\Api\RuleManagementInterface;

/**
 * Class Checker
 *
 * @package Aheadworks\Raf\Model\Advocate\Account
 */
class Checker
{
    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var AdvocateBalanceManagementInterface
     */
    private $advocateBalance;

    /**
     * @var CustomerGroupChecker
     */
    private $customerGroupChecker;

    /**
     * @var CustomerInvitationChecker
     */
    private $customerInvitationChecker;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param AdvocateBalanceManagementInterface $advocateBalanceManagement
     * @param RuleManagementInterface $ruleManagement
     * @param CustomerGroupChecker $customerGroupChecker
     * @param CustomerInvitationChecker $customerInvitationChecker
     */
    public function __construct(
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        AdvocateBalanceManagementInterface $advocateBalanceManagement,
        RuleManagementInterface $ruleManagement,
        CustomerGroupChecker $customerGroupChecker,
        CustomerInvitationChecker $customerInvitationChecker
    ) {
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->ruleManagement = $ruleManagement;
        $this->advocateBalance = $advocateBalanceManagement;
        $this->customerGroupChecker = $customerGroupChecker;
        $this->customerInvitationChecker = $customerInvitationChecker;
    }

    /**
     * Check if the customer can participate in the referral program
     *
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function canParticipateInReferralProgram($customerId, $websiteId)
    {
        if (empty($customerId)) {
            return false;
        }

        return $this->customerGroupChecker->isCustomerInReferralProgramGroup($customerId, $websiteId)
            && $this->customerInvitationChecker->isInvitationAllowedForCustomer($customerId, $websiteId);
    }

    /**
     * Check if the customer can use referral program and spend his balance
     *
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function canUseReferralProgramAndSpend($customerId, $websiteId)
    {
        return ($this->canParticipateInReferralProgram($customerId, $websiteId)
            || $this->advocateBalance->checkBalance($customerId, $websiteId))
            && $this->ruleManagement->getActiveRule($websiteId);
    }

    /**
     * Check if the customer is a participant of referral program
     *
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function isParticipantOfReferralProgram($customerId, $websiteId)
    {
        try {
            $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }

    /**
     * Check if referral link belongs to advocate
     *
     * @param string $referralLink
     * @param int|null $customerId
     * @param int $websiteId
     * @return bool
     */
    public function isReferralLinkBelongsToAdvocate($referralLink, $customerId, $websiteId)
    {
        try {
            $advocate = $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
            $result = $advocate->getReferralLink() == $referralLink;
        } catch (NoSuchEntityException $e) {
            $result = false;
        }

        return $result;
    }
}
