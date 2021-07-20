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

/**
 * Class DiscountPlugin
 *
 * @package Aheadworks\Raf\Plugin\Model\Quote\Total
 */
class DiscountPlugin extends AbstractResetTotalPlugin
{
    /**
     * @inheritdoc
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($quote->getCouponCode()) {
            $this->discountUsed = true;
            foreach ($shippingAssignment->getItems() as $item) {
                $item->setNoDiscount(1);
            }
        }
        return true;
    }
}
