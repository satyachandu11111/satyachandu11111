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
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\Raf\Model\Advocate\Account\RewardMessage as AdvocateRewardMessage;

/**
 * Class Viewer
 *
 * @package Aheadworks\Raf\Model\Advocate\Account
 */
class Viewer
{
    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrencyInterface;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RewardMessage
     */
    private $advocateRewardMessage;

    /**
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param PriceCurrencyInterface $priceCurrencyInterface
     * @param TimezoneInterface $localeDate
     * @param StoreManagerInterface $storeManager
     * @param AdvocateRewardMessage $advocateRewardMessage
     */
    public function __construct(
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        PriceCurrencyInterface $priceCurrencyInterface,
        TimezoneInterface $localeDate,
        StoreManagerInterface $storeManager,
        AdvocateRewardMessage $advocateRewardMessage
    ) {
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
        $this->advocateRewardMessage = $advocateRewardMessage;
    }

    /**
     * Retrieve advocate cumulative balance formatted
     *
     * @param int $customerId
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCumulativeBalanceFormatted($customerId, $storeId)
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $cumulativeAmount = $this->getAdvocate($customerId, $websiteId)->getCumulativeAmount();
        $cumulativePercentAmount = $this->getAdvocate($customerId, $websiteId)->getCumulativePercentAmount();

        $result = __('N/A');

        if ($cumulativeAmount) {
            $priceFormatted = $this->priceCurrencyInterface->convertAndFormat(
                $cumulativeAmount,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $storeId
            );

            $result = $priceFormatted;
        }

        if ($cumulativePercentAmount) {
            $cumulativePercentAmountFormatted = $cumulativePercentAmount . '%';
            $result = $cumulativeAmount
                ? $result . '; ' . $cumulativePercentAmountFormatted
                : $cumulativePercentAmountFormatted;
        }

        return $result;
    }

    /**
     * Retrieve advocate cumulative balance
     *
     * @param int $customerId
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBalanceExpiredFormatted($customerId, $storeId)
    {
        $store = $this->storeManager->getStore($storeId);
        $websiteId = $store->getWebsiteId();
        $expirationDate = $this->getAdvocate($customerId, $websiteId)->getExpirationDate();
        if (empty($expirationDate)) {
            $dateFormatted = '-';
        } else {
            $expirationDate = new \DateTime($expirationDate);
            $storeTimezone = $this->localeDate->getConfigTimezone(ScopeInterface::SCOPE_STORE, $store->getCode());
            $dateFormatted = $this->localeDate->formatDateTime(
                $expirationDate,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE,
                null,
                $storeTimezone
            );
        }

        return $dateFormatted;
    }

    /**
     * Retrieve advocate cumulative balance
     *
     * @param int $customerId
     * @param int $websiteId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInvitedFriendsCount($customerId, $websiteId)
    {
        return $this->getAdvocate($customerId, $websiteId)->getInvitedFriends();
    }

    /**
     * Get advocate reward message
     *
     * @param $customerId
     * @param $websiteId
     * @return \Magento\Framework\Phrase|string
     */
    public function getRewardMessage($customerId, $websiteId)
    {
        return $this->advocateRewardMessage->getMessage($customerId, $websiteId);
    }

    /**
     * Retrieve advocate entity
     *
     * @param int $customerId
     * @param int $websiteId
     * @return \Aheadworks\Raf\Api\Data\AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAdvocate($customerId, $websiteId)
    {
        return $this->advocateSummaryRepository->getByCustomerId($customerId, $websiteId);
    }
}
