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
namespace Aheadworks\Raf\Model\Advocate\Account\Checker;

use Aheadworks\Raf\Model\Config;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CustomerGroup
 * @package Aheadworks\Raf\Model\Advocate\Account\Checker
 */
class CustomerGroup
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Config $config
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Config $config,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->config = $config;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check if customer is in referral program group
     *
     * @param $customerId
     * @param $websiteId
     * @return bool
     */
    public function isCustomerInReferralProgramGroup($customerId, $websiteId)
    {
        $referralProgramGroups = explode(',', $this->config->getCustomerGroupsToJoinReferralProgram($websiteId));
        try {
            $customer = $this->customerRepository->getById($customerId);
            return (is_array($referralProgramGroups) && in_array($customer->getGroupId(), $referralProgramGroups));
        } catch (\Exception $e) {
        }
        return false;
    }
}
