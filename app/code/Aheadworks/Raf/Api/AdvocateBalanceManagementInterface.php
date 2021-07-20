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

use Aheadworks\Raf\Api\Data\TransactionInterface;

/**
 * Interface AdvocateBalanceManagementInterface
 * @api
 */
interface AdvocateBalanceManagementInterface
{
    /**
     * Check balance of an advocate
     *
     * @param int $customerId
     * @param int $websiteId
     * @return bool
     */
    public function checkBalance($customerId, $websiteId);

    /**
     * Get balance of an advocate
     *
     * @param int $customerId
     * @param int $websiteId
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBalance($customerId, $websiteId);

    /**
     * Get balance discount type
     *
     * @param int $customerId
     * @param int $websiteId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDiscountType($customerId, $websiteId);

    /**
     * Update balance
     *
     * @param int $customerId
     * @param int $websiteId
     * @param TransactionInterface $transaction
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateBalance($customerId, $websiteId, $transaction);
}
