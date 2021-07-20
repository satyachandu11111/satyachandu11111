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
namespace Aheadworks\Raf\Ui\DataProvider\Advocate\FriendPurchases;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FilterProcessor
 *
 * @package Aheadworks\Raf\Ui\DataProvider\Advocate\FriendPurchases
 */
class FilterProcessor
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve all stores for specific website
     *
     * @param int $websiteId
     * @return array
     */
    public function getStoreIds($websiteId)
    {
        $storeIds = [];
        $stores = $this->storeManager->getStores(false);
        foreach ($stores as $store) {
            if ($store->getWebsiteId() == $websiteId) {
                $storeIds[] = $store->getId();
            }
        }
        return $storeIds;
    }

    /**
     * Retrieve website id
     *
     * @param SearchCriteria $searchCriteria
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsiteId($searchCriteria)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == AdvocateSummaryInterface::WEBSITE_ID) {
                    $websiteId = $filter->getValue();
                }
            }
        }

        return $websiteId;
    }
}
