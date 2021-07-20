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
namespace Aheadworks\Raf\Model\Rule\Discount\Calculator\Shipping;

use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Calculator\Shipping
 */
class Processor
{
    /**
     * Retrieve base shipping amount
     *
     * @param AddressInterface $address
     * @return float
     */
    public function getTotalBaseShippingAmount($address)
    {
        if ($address->getBaseShippingAmountForDiscount() !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $baseShippingAmount = $baseShippingAmount - $address->getBaseShippingDiscountAmount()
            - $address->getBaseAwRewardPointsShippingAmount();

        return $baseShippingAmount;
    }

    /**
     * Retrieve shipping amount
     *
     * @param AddressInterface $address
     * @return float
     */
    public function getTotalShippingAmount($address)
    {
        if ($address->getShippingAmountForDiscount() !== null) {
            $shippingAmount = $address->getShippingAmountForDiscount();
        } else {
            $shippingAmount = $address->getShippingAmount();
        }
        $shippingAmount = $shippingAmount - $address->getShippingDiscountAmount()
            - $address->getAwRewardPointsShippingAmount();

        return $shippingAmount;
    }
}
