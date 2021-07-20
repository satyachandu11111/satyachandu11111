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
 * Interface TransactionManagementInterface
 * @api
 */
interface TransactionManagementInterface
{
    /**
     * Create transaction
     *
     * @param int $customerId
     * @param int $websiteId
     * @param string $action
     * @param float $amount
     * @param string $amountType
     * @param int|null $createdBy
     * @param string|null $adminComment
     * @param \Magento\Sales\Api\Data\OrderInterface[]|\Magento\Sales\Api\Data\CreditmemoInterface[]
     * |\Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Api\Data\CreditmemoInterface|null $entities
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Aheadworks\Raf\Api\Data\TransactionInterface
     */
    public function createTransaction(
        $customerId,
        $websiteId,
        $action,
        $amount,
        $amountType,
        $createdBy,
        $adminComment,
        $entities
    );
}
