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
namespace Aheadworks\Raf\Api;

/**
 * AdvocateSummary CRUD interface
 * @api
 */
interface AdvocateSummaryRepositoryInterface
{
    /**
     * Save item to advocate summary
     *
     * @param \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface $advocateSummaryItem
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Raf\Api\Data\AdvocateSummaryInterface $advocateSummaryItem);

    /**
     * Retrieve advocate summary item by id
     *
     * @param int $advocateSummaryItemId
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($advocateSummaryItemId);

    /**
     * Retrieve advocate summary item by customer id and website id
     *
     * @param int $customerId
     * @param int $websiteId
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId, $websiteId);

    /**
     * Retrieve advocate summary item by referral link and website id
     *
     * @param string $referralLink
     * @param int $websiteId
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByReferralLink($referralLink, $websiteId);

    /**
     * Retrieve advocate summary items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummarySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
