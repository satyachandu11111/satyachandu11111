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
namespace Aheadworks\Raf\Plugin\CustomerData;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\Customer;
use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Model\Advocate\Url;

/**
 * Class CustomerPlugin
 *
 * @package Aheadworks\Raf\Plugin\CustomerData
 */
class CustomerPlugin
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var AdvocateManagementInterface
     */
    private $advocateManagement;

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CurrentCustomer $currentCustomer
     * @param AdvocateManagementInterface $advocateManagement
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrentCustomer $currentCustomer,
        AdvocateManagementInterface $advocateManagement,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository
    ) {
        $this->storeManager = $storeManager;
        $this->advocateManagement = $advocateManagement;
        $this->advocateRepository = $advocateSummaryRepository;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param Customer $subject
     * @param string[] $result
     * @return string[]
     */
    public function afterGetSectionData($subject, $result)
    {
        $customerId = $this->currentCustomer->getCustomerId();
        if (!$customerId) {
            return $result;
        }
        try {
            $websiteId = $this->storeManager->getWebsite()->getId();
            if ($this->advocateManagement->isParticipantOfReferralProgram($customerId, $websiteId)
                && $this->advocateManagement->canParticipateInReferralProgram($customerId, $websiteId)
            ) {
                $advocate = $this->advocateRepository->getByCustomerId($customerId, $websiteId);
                $result['awRafCanUseRafProgram'] = true;
                $result['awRafExternalLinkParam'] = Url::REFERRAL_PARAM;
                $result['awRafExternalLinkValue'] = $advocate->getReferralLink();
                $result['awRafBaseUrl'] = $this->advocateManagement->getReferralUrl($customerId, $websiteId);
                return $result;
            }
        } catch (\Exception $exception) {
        }

        return $result;
    }
}
