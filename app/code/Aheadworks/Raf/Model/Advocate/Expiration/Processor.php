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
namespace Aheadworks\Raf\Model\Advocate\Expiration;

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\ReminderStatus;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\Raf\Model\Advocate
 */
class Processor
{
    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param Config $config
     */
    public function __construct(
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        Config $config
    ) {
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->config = $config;
    }

    /**
     * Retrieve advocates balance to expire
     *
     * @return AdvocateSummaryInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getAdvocatesBalanceToExpire()
    {
        $advocatesBalanceToExpire = [];
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteId = $website->getId();
            $this->searchCriteriaBuilder
                ->addFilter(AdvocateSummaryInterface::WEBSITE_ID, $websiteId)
                ->addFilter('expired', $this->getWebsiteDate($website));

            $advocates = $this->advocateSummaryRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();

            $advocatesBalanceToExpire = array_merge($advocatesBalanceToExpire, $advocates);
        }

        return $advocatesBalanceToExpire;
    }

    /**
     * Retrieve advocates at which the balance expires
     *
     * @return AdvocateSummaryInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getAdvocatesWhichBalanceExpires()
    {
        $advocatesWhichBalanceExpires = [];
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteId = $website->getId();
            $sendEmailReminderInDays = $this->config->getSendEmailReminderInDays($websiteId);
            if ($sendEmailReminderInDays == 0) {
                continue;
            }
            $this->searchCriteriaBuilder
                ->addFilter(AdvocateSummaryInterface::WEBSITE_ID, $websiteId)
                ->addFilter(AdvocateSummaryInterface::CUMULATIVE_AMOUNT, 0, 'gt')
                ->addFilter(
                    AdvocateSummaryInterface::REMINDER_STATUS,
                    [ReminderStatus::READY_TO_BE_SENT, ReminderStatus::FAILED],
                    'in'
                )
                ->addFilter('expired', $this->getWebsiteDate($website, $sendEmailReminderInDays + 1));

            $advocates = $this->advocateSummaryRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();
            $advocatesWhichBalanceExpires = array_merge($advocatesWhichBalanceExpires, $advocates);
        }

        return $advocatesWhichBalanceExpires;
    }

    /**
     * Retrieve website date
     *
     * @param WebsiteInterface $website
     * @param int $addDays
     * @return string
     * @throws \Exception
     */
    private function getWebsiteDate($website, $addDays = null)
    {
        $websiteTimezone = $this->localeDate->getConfigTimezone(ScopeInterface::SCOPE_WEBSITE, $website->getCode());
        $now = new \DateTime(null, new \DateTimeZone($websiteTimezone));
        if ($addDays) {
            $now->add(new \DateInterval('P' . $addDays . 'D'));
        }
        $now->setTimezone(new \DateTimeZone('UTC'));

        return $now->format('Y-m-d');
    }
}
