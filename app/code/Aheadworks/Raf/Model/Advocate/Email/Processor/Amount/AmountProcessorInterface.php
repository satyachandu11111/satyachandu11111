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
namespace Aheadworks\Raf\Model\Advocate\Email\Processor\Amount;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Email\EmailMetadataInterface;

/**
 * Interface AmountProcessorInterface
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\Amount
 */
interface AmountProcessorInterface
{
    /**
     * Process amount email data
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @param float $amount
     * @param string $amountType
     * @param int $storeId
     * @return EmailMetadataInterface
     */
    public function process($advocateSummary, $amount, $amountType, $storeId);
}
