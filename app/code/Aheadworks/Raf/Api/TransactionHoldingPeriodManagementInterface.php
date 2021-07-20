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
 * Interface TransactionHoldingPeriodManagementInterface
 *
 * @package Aheadworks\Raf\Api
 */
interface TransactionHoldingPeriodManagementInterface
{
    /**
     * Process transactions with expired holding period
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processExpiredTransactions();

    /**
     * Cancel transaction on order cancellation
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancelTransactionForCanceledOrder($order);
}
