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
namespace Aheadworks\Raf\Plugin\Model\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Interface ResetTotalInterface for plugins used to reset totals
 *
 * @package Aheadworks\Raf\Plugin\Model\Quote\Total
 */
interface ResetTotalInterface
{
    /**
     * Before collect method.
     *
     * Reset items for $shippingAssignment.
     * So in many cases it prevent total to be processed
     *
     * @param AbstractTotal $subject
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return array
     */
    public function beforeCollect(
        AbstractTotal $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    );

    /**
     * After collect method.
     *
     * Restore items for $shippingAssignment.
     * It is required to restore items for other
     * totals processing
     *
     * @param AbstractTotal $subject
     * @param AbstractTotal $result
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return array
     */
    public function afterCollect(
        AbstractTotal $subject,
        AbstractTotal $result,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    );
}
