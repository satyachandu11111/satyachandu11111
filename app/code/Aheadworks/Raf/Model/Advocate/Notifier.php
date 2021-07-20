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
namespace Aheadworks\Raf\Model\Advocate;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\Data\TransactionInterface;
use Aheadworks\Raf\Model\Email\Sender;
use Magento\Framework\Exception\MailException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\Raf\Model\Advocate\Email\Processor\Amount\Pool as EmailAmountProcessorPool;

/**
 * Class Notifier
 *
 * @package Aheadworks\Raf\Model\Advocate
 */
class Notifier
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EmailAmountProcessorPool
     */
    private $emailAmountProcessorPool;

    /**
     * @param Sender $sender
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param EmailAmountProcessorPool $emailAmountProcessorPool
     */
    public function __construct(
        Sender $sender,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        EmailAmountProcessorPool $emailAmountProcessorPool
    ) {
        $this->sender = $sender;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->emailAmountProcessorPool = $emailAmountProcessorPool;
    }

    /**
     * Notify about new friend
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @param TransactionInterface $transaction
     * @param int $storeId
     * @return bool
     */
    public function notifyAboutNewFriend($advocateSummary, $transaction, $storeId)
    {
        $emailMetadata = $this->emailAmountProcessorPool->get(EmailAmountProcessorPool::NEW_FRIEND)->process(
            $advocateSummary,
            $transaction->getAmount(),
            $transaction->getAmountType(),
            $storeId
        );
        try {
            $this->sender->send($emailMetadata);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }
        return true;
    }

    /**
     * Notify about balance expired
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @param TransactionInterface $transaction
     * @param int|null $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function notifyAboutBalanceExpired($advocateSummary, $transaction, $storeId = null)
    {
        $storeId = $storeId ? : $this->getDefaultStoreIdByWebsiteId($advocateSummary->getWebsiteId());
        $emailMetadata = $this->emailAmountProcessorPool->get(EmailAmountProcessorPool::EXPIRATION)->process(
            $advocateSummary,
            $transaction->getAmount(),
            $transaction->getAmountType(),
            $storeId
        );
        try {
            $this->sender->send($emailMetadata);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }
        return true;
    }

    /**
     * Notify about expire balance
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @param float $amount
     * @param string $amountType
     * @param int|null $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function expirationReminder($advocateSummary, $amount, $amountType, $storeId = null)
    {
        $storeId = $storeId ? : $this->getDefaultStoreIdByWebsiteId($advocateSummary->getWebsiteId());
        $emailMetadata = $this->emailAmountProcessorPool->get(EmailAmountProcessorPool::EXPIRATION_REMINDER)->process(
            $advocateSummary,
            $amount,
            $amountType,
            $storeId
        );
        try {
            $this->sender->send($emailMetadata);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }
        return true;
    }

    /**
     * Retrieve default store id by website id
     *
     * @param int $websiteId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDefaultStoreIdByWebsiteId($websiteId)
    {
        $groupId = $this->storeManager->getWebsite($websiteId)->getDefaultGroupId();
        return $this->storeManager->getGroup($groupId)->getDefaultStoreId();
    }
}
