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
namespace Aheadworks\Raf\Model\Transaction\Processor;

use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Raf\Model\Transaction\Processor
 */
interface ProcessorInterface
{
    /**
     * Transaction processor
     *
     * @param int $customerId
     * @param int $websiteId
     * @param string $action
     * @param float $amount
     * @param string $amountType
     * @param $createdBy|null
     * @param $adminComment|null
     * @param OrderInterface[]|CreditmemoInterface[]|null $entities
     * @return TransactionInterface
     */
    public function process(
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
