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
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Raf\Model\Source\Customer\WhoCanInviteType;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CustomerInvitation
 * @package Aheadworks\Raf\Model\Advocate\Account\Checker
 */
class CustomerInvitation
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if customer is able to invite friends
     *
     * @param $customerId
     * @param $websiteId
     * @return bool
     */
    public function isInvitationAllowedForCustomer($customerId, $websiteId)
    {
        if ($this->config->getWhoCanInvite($websiteId) == WhoCanInviteType::CUSTOMERS_WITH_PURCHASES) {
            return $this->hasCustomerPlacedOrder($customerId, $websiteId);
        }
        return true;
    }

    /**
     * Check if customer has any orders placed previously on specific website
     *
     * @param $customerId
     * @param $websiteId
     * @return bool
     */
    private function hasCustomerPlacedOrder($customerId, $websiteId)
    {
        $stores = implode(",", $this->getStoresByWebsite($websiteId));
        $orderStatuses = $this->config->getOrderStatusesToAllowInvitation($websiteId);

        $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $this->searchCriteriaBuilder->addFilter(OrderInterface::STATUS, $orderStatuses, 'in');
        $this->searchCriteriaBuilder->addFilter(OrderInterface::STORE_ID, $stores, 'in');

        $orders = $this->orderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return !empty($orders);
    }

    /**
     * Get all stores for specific website
     *
     * @param int $websiteId
     * @return array
     */
    private function getStoresByWebsite($websiteId)
    {
        $storeArray = [];
        $stores = $this->storeManager->getStores(false);
        foreach ($stores as $store) {
            if ($store->getWebsiteId() == $websiteId) {
                $storeArray[] = $store->getId();
            }
        }
        return $storeArray;
    }
}
